<x-layouts.app title="Teacher Dashboard">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="dashboard" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-4 md:space-y-5">

                {{-- ── GREETING HERO ── --}}
                <div class="d d1 relative overflow-hidden af-card px-7 py-7 md:px-10 md:py-9">
                    <div class="blob-a absolute -top-16 -right-12 size-64 rounded-full bg-secondary/10 blur-3xl pointer-events-none"></div>
                    <div class="blob-b absolute -bottom-10 left-1/3 size-44 rounded-full bg-primary/10 blur-2xl pointer-events-none"></div>
                    <div class="af-dots absolute inset-0 pointer-events-none"></div>
                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                        <div class="flex items-center gap-4">
                            @if ($user->avatarUrl)
                                <img src="{{ $user->avatarUrl }}" class="size-14 rounded-2xl object-cover ring-2 ring-secondary/30 shadow-lg shadow-secondary/10 shrink-0" alt="">
                            @else
                                <div class="size-14 rounded-2xl bg-secondary/12 flex items-center justify-center text-xl font-black text-secondary shrink-0 ring-1 ring-secondary/15">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            @endif
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Welcome back</p>
                                <h1 class="mt-0.5 text-3xl md:text-4xl font-black tracking-tight">{{ $user->name }}</h1>
                                <p class="mt-1.5 text-sm text-base-content/40">{{ now()->format('l, F j') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            @if ($pendingExcuses > 0)
                                <a href="{{ route('teacher.excuses.index') }}">
                                    <x-ui.badge variant="warning" dot>{{ $pendingExcuses }} pending</x-ui.badge>
                                </a>
                            @endif
                            <x-ui.badge variant="secondary">Teacher</x-ui.badge>
                        </div>
                    </div>
                </div>

                {{-- ── STATS ── --}}
                <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
                    <div class="d d2">
                        <x-dashboard.stat-card label="My Classes" :value="$myClasses" color="primary"
                            :href="route('teacher.classes.index')"
                            icon='<path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>'
                        />
                    </div>
                    <div class="d d3">
                        <x-dashboard.stat-card label="Total Students" :value="$totalStudents" color="success"
                            icon='<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
                        />
                    </div>
                    <div class="d d4">
                        <x-dashboard.stat-card label="Sessions This Month" :value="$sessionsThisMonth" color="info"
                            icon='<rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>'
                        />
                    </div>
                    <div class="d d5">
                        <x-dashboard.stat-card label="Avg Attendance Rate" :value="$avgAttendanceRate . '%'" color="warning"
                            icon='<path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
                        />
                    </div>
                </div>

                {{-- ── CHARTS ── --}}
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                    <div class="d d6 lg:col-span-3">
                        <x-dashboard.chart-card
                            title="Per-Class Attendance Rate"
                            chart-type="bar"
                            :chart-data="$barData"
                            canvas-id="teacher-bar-chart"
                        />
                    </div>
                    <div class="d d7 lg:col-span-2">
                        <x-dashboard.chart-card
                            title="Attendance Distribution"
                            chart-type="pie"
                            :chart-data="$pieData"
                            canvas-id="teacher-pie-chart"
                        />
                    </div>
                </div>


                {{-- ── UPCOMING SESSIONS ── --}}
                <div class="d d8 af-card overflow-hidden !p-0">
                    <x-ui.section-header label="Upcoming Sessions" :action-href="route('teacher.classes.index')" action-label="View classes" />
                    @if ($upcomingSessions->isEmpty())
                        <x-ui.empty-state title="No upcoming sessions" description="No sessions scheduled yet." icon='<rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>' />
                    @else
                        <div class="divide-y divide-base-300/30">
                            @foreach ($upcomingSessions as $upcoming)
                                @php
                                    $uVariant = match ($upcoming->status->value) {
                                        'Active'    => 'success',
                                        'Scheduled' => 'info',
                                        default     => 'neutral',
                                    };
                                @endphp
                                <a href="{{ route('teacher.sessions.show', $upcoming) }}" class="flex items-center justify-between gap-4 px-5 py-3.5 hover:bg-base-content/[.03] transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="shrink-0 size-9 rounded-xl bg-info/10 flex items-center justify-center ring-1 ring-info/15">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 text-info"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold truncate">{{ $upcoming->schoolClass->name }}</p>
                                            <p class="text-xs text-base-content/40">{{ $upcoming->start_time->format('M d, g:i A') }}</p>
                                        </div>
                                    </div>
                                    <x-ui.badge :variant="$uVariant" size="xs">{{ $upcoming->status->value }}</x-ui.badge>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ── RECENT SESSIONS ── --}}
                <div class="d d9 af-card overflow-hidden !p-0">
                    <x-ui.section-header label="Recent Sessions" :action-href="route('teacher.classes.index')" action-label="View classes" />
                    @if ($recentSessions->isEmpty())
                        <x-ui.empty-state title="No sessions yet" description="Start one from a class page." icon='<rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>' />
                    @else
                        <div class="divide-y divide-base-300/30">
                            @foreach ($recentSessions as $session)
                                @php
                                    $sVariant = match ($session->status) {
                                        'Active'    => 'success',
                                        'Completed' => 'neutral',
                                        'Cancelled' => 'error',
                                        default     => 'info',
                                    };
                                @endphp
                                <div class="flex items-center justify-between gap-4 px-5 py-3.5 hover:bg-base-content/[.03] transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="shrink-0 size-9 rounded-xl bg-base-content/5 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 text-base-content/40"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold truncate">{{ $session->class_name }}</p>
                                            <p class="text-xs text-base-content/40">{{ \Carbon\Carbon::parse($session->created_at)->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <x-ui.badge :variant="$sVariant" size="xs">{{ $session->status }}</x-ui.badge>
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
    </script>
</x-layouts.app>
