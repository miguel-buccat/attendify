<x-layouts.app title="Student Dashboard">
    <style>
        @keyframes d-up { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }
        .d { animation: d-up .55s cubic-bezier(.16,1,.3,1) both; }
        .d1 { animation-delay: .00s; } .d2 { animation-delay: .07s; } .d3 { animation-delay: .14s; }
        .d4 { animation-delay: .21s; } .d5 { animation-delay: .28s; } .d6 { animation-delay: .35s; }
        .d7 { animation-delay: .42s; } .d8 { animation-delay: .49s; } .d9 { animation-delay: .56s; }
        @keyframes blob-drift {
            0%,100% { transform: translate(0,0) scale(1); }
            33%  { transform: translate(-12px,10px) scale(1.07); }
            66%  { transform: translate(10px,-8px) scale(.95); }
        }
        .blob-a { animation: blob-drift 9s ease-in-out infinite; }
        .blob-b { animation: blob-drift 11s ease-in-out infinite reverse; animation-delay: 2s; }
        .attendance-ring {
            transition: stroke-dashoffset 1.3s cubic-bezier(.16,1,.3,1);
        }
    </style>

    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="dashboard" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-4 md:space-y-5">

                {{-- ── GREETING + ATTENDANCE HERO ── --}}
                @php
                    $circ = round(2 * M_PI * 52, 2);
                    $offset = round($circ * (1 - $attendanceRate / 100), 2);
                @endphp
                <div class="d d1 relative overflow-hidden rounded-3xl bg-base-100 border border-base-300/30 px-7 py-7 md:px-10 md:py-9">
                    <div class="blob-a absolute -top-16 left-1/4 size-64 rounded-full bg-accent/10 blur-3xl pointer-events-none"></div>
                    <div class="blob-b absolute -bottom-10 -right-8 size-44 rounded-full bg-primary/10 blur-2xl pointer-events-none"></div>
                    <div class="absolute inset-0 pointer-events-none" style="background-image:radial-gradient(circle,oklch(var(--bc)/.06) 1px,transparent 1px);background-size:22px 22px;"></div>

                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-6 sm:gap-10">

                        {{-- Left: greeting + status counts --}}
                        <div class="flex-1 space-y-6">
                            <div class="flex items-start justify-between sm:justify-start gap-4">
                                <div class="flex items-center gap-4">
                                    @if ($user->avatarUrl)
                                        <img src="{{ $user->avatarUrl }}" class="size-14 rounded-2xl object-cover ring-2 ring-base-300/50 shrink-0" alt="">
                                    @else
                                        <div class="size-14 rounded-2xl bg-accent/10 flex items-center justify-center text-xl font-black text-accent shrink-0">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                    @endif
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Welcome back</p>
                                        <h1 class="mt-0.5 text-3xl md:text-4xl font-black tracking-tight">{{ $user->name }}</h1>
                                        <p class="mt-1.5 text-sm text-base-content/40">{{ now()->format('l, F j') }}</p>
                                    </div>
                                </div>
                                <span class="sm:hidden inline-flex items-center rounded-full bg-accent/10 border border-accent/20 text-accent text-sm font-bold px-3.5 py-1.5 shrink-0">Student</span>
                            </div>

                            {{-- Attendance breakdown --}}
                            <div class="grid grid-cols-4 gap-2 sm:gap-4">
                                <div>
                                    <p class="text-2xl sm:text-3xl font-black text-success tabular-nums" data-count="{{ $presentCount }}">{{ $presentCount }}</p>
                                    <p class="text-xs text-base-content/40 mt-0.5">Present</p>
                                </div>
                                <div>
                                    <p class="text-2xl sm:text-3xl font-black text-warning tabular-nums" data-count="{{ $lateCount }}">{{ $lateCount }}</p>
                                    <p class="text-xs text-base-content/40 mt-0.5">Late</p>
                                </div>
                                <div>
                                    <p class="text-2xl sm:text-3xl font-black text-error tabular-nums" data-count="{{ $absentCount }}">{{ $absentCount }}</p>
                                    <p class="text-xs text-base-content/40 mt-0.5">Absent</p>
                                </div>
                                <div>
                                    <p class="text-2xl sm:text-3xl font-black text-info tabular-nums" data-count="{{ $excusedCount }}">{{ $excusedCount }}</p>
                                    <p class="text-xs text-base-content/40 mt-0.5">Excused</p>
                                </div>
                            </div>
                        </div>

                        {{-- Right: attendance ring --}}
                        <div class="flex flex-col items-center gap-2 shrink-0">
                            <div class="relative size-32 sm:size-36">
                                <svg class="size-full -rotate-90" viewBox="0 0 120 120">
                                    <circle cx="60" cy="60" r="52" fill="none" stroke="oklch(var(--bc)/.08)" stroke-width="10"/>
                                    <circle cx="60" cy="60" r="52" fill="none" stroke="oklch(var(--p))" stroke-width="10"
                                            stroke-linecap="round"
                                            stroke-dasharray="{{ $circ }}"
                                            stroke-dashoffset="{{ $circ }}"
                                            class="attendance-ring"
                                            data-target="{{ $offset }}"/>
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl sm:text-3xl font-black tabular-nums text-primary" data-count="{{ $attendanceRate }}" data-suffix="%">{{ $attendanceRate }}%</span>
                                </div>
                            </div>
                            <span class="hidden sm:inline-flex items-center rounded-full bg-accent/10 border border-accent/20 text-accent text-xs font-bold px-3 py-1">Student</span>
                            <p class="text-xs text-base-content/40 text-center leading-tight">Attendance<br>Rate</p>
                        </div>
                    </div>
                </div>

                {{-- ── STATS ── --}}
                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    <div class="d d2">
                        <x-dashboard.stat-card label="My Classes" :value="$myClasses" color="primary"
                            :href="route('student.classes.index')"
                            icon='<path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>'
                        />
                    </div>
                    <div class="d d3">
                        <x-dashboard.stat-card label="Attendance Rate" :value="$attendanceRate . '%'" color="secondary"
                            :href="route('student.attendance.index')"
                            icon='<path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
                        />
                    </div>
                </div>

                {{-- ── QUICK ACTIONS ── --}}
                <div class="d d4 rounded-2xl border border-base-300/40 bg-base-100 p-5">
                    <p class="text-xs uppercase tracking-widest text-base-content/35 font-semibold mb-3">Quick Actions</p>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('student.scan.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary/10 text-primary border border-primary/15 text-sm font-medium hover:bg-primary/15 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M3 7V5a2 2 0 0 1 2-2h2m10 0h2a2 2 0 0 1 2 2v2m0 10v2a2 2 0 0 1-2 2h-2M3 17v2a2 2 0 0 0 2 2h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 12h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            Scan QR
                        </a>
                        <a href="{{ route('student.classes.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-base-200 text-base-content/70 border border-base-300/50 text-sm font-medium hover:bg-base-300/50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            My Classes
                        </a>
                        <a href="{{ route('student.attendance.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-base-200 text-base-content/70 border border-base-300/50 text-sm font-medium hover:bg-base-300/50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Attendance History
                        </a>
                        <a href="{{ route('student.excuses.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-base-200 text-base-content/70 border border-base-300/50 text-sm font-medium hover:bg-base-300/50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            File Excuse
                        </a>
                    </div>
                </div>

                {{-- ── CHARTS ── --}}
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                    <div class="d d5 lg:col-span-3">
                        <x-dashboard.chart-card
                            title="My Attendance Over Time"
                            chart-type="line"
                            :chart-data="$lineData"
                            canvas-id="student-line-chart"
                        />
                    </div>
                    <div class="d d6 lg:col-span-2">
                        <x-dashboard.chart-card
                            title="Status Breakdown"
                            chart-type="pie"
                            :chart-data="$pieData"
                            canvas-id="student-pie-chart"
                        />
                    </div>
                </div>

                {{-- ── UPCOMING SESSIONS ── --}}
                <div class="d d7 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-base-300/30 flex items-center justify-between">
                        <h3 class="font-semibold text-sm">Upcoming Sessions</h3>
                        <a href="{{ route('student.calendar.index') }}" class="text-xs text-primary hover:underline">Calendar →</a>
                    </div>
                    @if ($upcomingSessions->isEmpty())
                        <div class="px-5 py-10 text-center">
                            <p class="text-sm text-base-content/40">No upcoming sessions.</p>
                        </div>
                    @else
                        <div class="divide-y divide-base-300/30">
                            @foreach ($upcomingSessions as $upcoming)
                                <div class="flex items-center justify-between gap-4 px-5 py-3.5 hover:bg-base-200/40 transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="shrink-0 size-8 rounded-lg bg-info/10 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 text-info"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium truncate">{{ $upcoming->schoolClass->name }}</p>
                                            <p class="text-xs text-base-content/40">{{ $upcoming->start_time->format('M d, g:i A') }} · {{ $upcoming->modality->value }}</p>
                                        </div>
                                    </div>
                                    <span class="shrink-0 inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-info bg-info/10 border-info/20">{{ $upcoming->start_time->diffForHumans() }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ── RECENT ATTENDANCE ── --}}
                <div class="d d8 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-base-300/30 flex items-center justify-between">
                        <h3 class="font-semibold text-sm">Recent Attendance</h3>
                        <a href="{{ route('student.attendance.index') }}" class="text-xs text-primary hover:underline">View all →</a>
                    </div>
                    @if ($recentRecords->isEmpty())
                        <div class="px-5 py-10 text-center">
                            <p class="text-sm text-base-content/40">No attendance records yet.</p>
                        </div>
                    @else
                        <div class="divide-y divide-base-300/30">
                            @foreach ($recentRecords as $record)
                                @php
                                    $statusStyle = match ($record->status) {
                                        \App\Enums\AttendanceStatus::Present => 'text-success bg-success/10 border-success/20',
                                        \App\Enums\AttendanceStatus::Late    => 'text-warning bg-warning/10 border-warning/20',
                                        \App\Enums\AttendanceStatus::Absent  => 'text-error bg-error/10 border-error/20',
                                        \App\Enums\AttendanceStatus::Excused => 'text-info bg-info/10 border-info/20',
                                        default => 'text-base-content/50 bg-base-200 border-base-300/50',
                                    };
                                    $dotColor = match ($record->status) {
                                        \App\Enums\AttendanceStatus::Present => 'bg-success',
                                        \App\Enums\AttendanceStatus::Late    => 'bg-warning',
                                        \App\Enums\AttendanceStatus::Absent  => 'bg-error',
                                        \App\Enums\AttendanceStatus::Excused => 'bg-info',
                                        default => 'bg-base-300',
                                    };
                                @endphp
                                <div class="flex items-center justify-between gap-4 px-5 py-3.5 hover:bg-base-200/40 transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="shrink-0 size-2 rounded-full {{ $dotColor }}"></div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium truncate">{{ $record->classSession?->schoolClass?->name ?? 'Unknown Class' }}</p>
                                            <p class="text-xs text-base-content/40">{{ $record->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <span class="shrink-0 inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusStyle }}">{{ $record->status->value }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </main>
    </div>

    @vite('resources/js/charts.js')

    <script>
    // Counter animation
    document.querySelectorAll('[data-count]').forEach(el => {
        const end = parseFloat(el.dataset.count);
        if (isNaN(end)) return;
        const suffix = el.dataset.suffix || '';
        const dur = 900, t0 = performance.now();
        const isInt = Number.isInteger(end);
        const step = now => {
            const t = Math.min((now - t0) / dur, 1);
            const e = 1 - Math.pow(1 - t, 3);
            el.textContent = (isInt ? Math.round(e * end) : (e * end).toFixed(1)) + suffix;
            if (t < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    });
    // Attendance ring animation
    setTimeout(() => {
        document.querySelectorAll('.attendance-ring').forEach(ring => {
            const target = ring.dataset.target;
            if (target != null) ring.style.strokeDashoffset = target;
        });
    }, 150);
    </script>
</x-layouts.app>
