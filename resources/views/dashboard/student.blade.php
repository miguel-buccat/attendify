<x-layouts.app title="Student Dashboard">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="dashboard" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-4 md:space-y-5">

                {{-- ── GREETING + ATTENDANCE HERO ── --}}
                @php
                    $circ = round(2 * M_PI * 52, 2);
                    $offset = round($circ * (1 - $attendanceRate / 100), 2);
                @endphp
                <div class="d d1 relative overflow-hidden af-card px-7 py-7 md:px-10 md:py-9">
                    <div class="blob-a absolute -top-16 left-1/4 size-64 rounded-full bg-accent/10 blur-3xl pointer-events-none"></div>
                    <div class="blob-b absolute -bottom-10 -right-8 size-44 rounded-full bg-primary/10 blur-2xl pointer-events-none"></div>
                    <div class="af-dots absolute inset-0 pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-6 sm:gap-10">

                        {{-- Left: greeting + status counts --}}
                        <div class="flex-1 space-y-6">
                            <div class="flex items-start justify-between sm:justify-start gap-4">
                                <div class="flex items-center gap-4">
                                    @if ($user->avatarUrl)
                                        <img src="{{ $user->avatarUrl }}" class="size-14 rounded-2xl object-cover ring-2 ring-accent/30 shadow-lg shadow-accent/10 shrink-0" alt="">
                                    @else
                                        <div class="size-14 rounded-2xl bg-accent/12 flex items-center justify-center text-xl font-black text-accent shrink-0 ring-1 ring-accent/15">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                    @endif
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Welcome back</p>
                                        <h1 class="mt-0.5 text-3xl md:text-4xl font-black tracking-tight">{{ $user->name }}</h1>
                                        <p class="mt-1.5 text-sm text-base-content/40">{{ now()->format('l, F j') }}</p>
                                    </div>
                                </div>
                                <span class="sm:hidden"><x-ui.badge variant="accent">Student</x-ui.badge></span>
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
                            <span class="hidden sm:block"><x-ui.badge variant="accent" size="xs">Student</x-ui.badge></span>
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
                <div class="d d7 af-card overflow-hidden !p-0">
                    <x-ui.section-header label="Upcoming Sessions" :action-href="route('student.calendar.index')" action-label="Calendar" />
                    @if ($upcomingSessions->isEmpty())
                        <x-ui.empty-state title="No upcoming sessions" description="Check back later for new sessions." icon='<rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>' />
                    @else
                        <div class="divide-y divide-base-300/30">
                            @foreach ($upcomingSessions as $upcoming)
                                <div class="flex items-center justify-between gap-4 px-5 py-3.5 hover:bg-base-content/[.03] transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="shrink-0 size-9 rounded-xl bg-info/10 flex items-center justify-center ring-1 ring-info/15">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 text-info"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold truncate">{{ $upcoming->schoolClass->name }}</p>
                                            <p class="text-xs text-base-content/40">{{ $upcoming->start_time->format('M d, g:i A') }} · {{ $upcoming->modality->value }}</p>
                                        </div>
                                    </div>
                                    <x-ui.badge variant="info" size="xs">{{ $upcoming->start_time->diffForHumans() }}</x-ui.badge>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ── RECENT ATTENDANCE ── --}}
                <div class="d d8 af-card overflow-hidden !p-0">
                    <x-ui.section-header label="Recent Attendance" :action-href="route('student.attendance.index')" action-label="View all" />
                    @if ($recentRecords->isEmpty())
                        <x-ui.empty-state title="No attendance records" description="Your records will appear here." icon='<path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m-6 9 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>' />
                    @else
                        <div class="divide-y divide-base-300/30">
                            @foreach ($recentRecords as $record)
                                @php
                                    $rVariant = match ($record->status) {
                                        \App\Enums\AttendanceStatus::Present => 'success',
                                        \App\Enums\AttendanceStatus::Late    => 'warning',
                                        \App\Enums\AttendanceStatus::Absent  => 'error',
                                        \App\Enums\AttendanceStatus::Excused => 'info',
                                        default => 'neutral',
                                    };
                                @endphp
                                <div class="flex items-center justify-between gap-4 px-5 py-3.5 hover:bg-base-content/[.03] transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="shrink-0 size-2.5 rounded-full bg-{{ $rVariant }}"></div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold truncate">{{ $record->classSession?->schoolClass?->name ?? 'Unknown Class' }}</p>
                                            <p class="text-xs text-base-content/40">{{ $record->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <x-ui.badge :variant="$rVariant" size="xs">{{ $record->status->value }}</x-ui.badge>
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
    setTimeout(() => {
        document.querySelectorAll('.attendance-ring').forEach(ring => {
            const target = ring.dataset.target;
            if (target != null) ring.style.strokeDashoffset = target;
        });
    }, 150);
    </script>
</x-layouts.app>
