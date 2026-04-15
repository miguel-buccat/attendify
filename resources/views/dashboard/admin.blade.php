<x-layouts.app title="Admin Dashboard">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="dashboard" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-4 md:space-y-5">

                {{-- ── GREETING HERO ── --}}
                <div class="d d1 relative overflow-hidden af-card px-7 py-7 md:px-10 md:py-9">
                    <div class="blob-a absolute -top-16 -right-12 size-64 rounded-full bg-primary/10 blur-3xl pointer-events-none"></div>
                    <div class="blob-b absolute -bottom-10 left-1/3 size-44 rounded-full bg-secondary/10 blur-2xl pointer-events-none"></div>
                    <div class="af-dots absolute inset-0 pointer-events-none"></div>
                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                        <div class="flex items-center gap-4">
                            @if ($user->avatarUrl)
                                <img src="{{ $user->avatarUrl }}" class="size-14 rounded-2xl object-cover ring-2 ring-primary/30 shadow-lg shadow-primary/10 shrink-0" alt="">
                            @else
                                <div class="size-14 rounded-2xl bg-primary/12 flex items-center justify-center text-xl font-black text-primary shrink-0 ring-1 ring-primary/15">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            @endif
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Welcome back</p>
                                <h1 class="mt-0.5 text-3xl md:text-4xl font-black tracking-tight">{{ $user->name }}</h1>
                                <p class="mt-1.5 text-sm text-base-content/40">{{ now()->format('l, F j') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            @if ($pendingExcuses > 0)
                                <x-ui.badge variant="warning" dot>{{ $pendingExcuses }} pending</x-ui.badge>
                            @endif
                            <x-ui.badge variant="primary">Admin</x-ui.badge>
                        </div>
                    </div>
                </div>

                {{-- ── STATS ── --}}
                <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
                    <div class="d d2">
                        <x-dashboard.stat-card label="Total Users" :value="$totalUsers" color="primary"
                            :href="route('admin.users.index')"
                            icon='<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 4v6m3-3h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
                        />
                    </div>
                    <div class="d d3">
                        <x-dashboard.stat-card label="Active Classes" :value="$activeClasses" color="success"
                            icon='<path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>'
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
                            title="Attendance Trend (30 Days)"
                            chart-type="line"
                            :chart-data="$lineData"
                            canvas-id="admin-line-chart"
                        />
                    </div>
                    <div class="d d7 lg:col-span-2">
                        <x-dashboard.chart-card
                            title="Attendance Distribution"
                            chart-type="pie"
                            :chart-data="$pieData"
                            canvas-id="admin-pie-chart"
                        />
                    </div>
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
