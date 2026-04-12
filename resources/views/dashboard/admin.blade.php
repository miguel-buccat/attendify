<x-layouts.app title="Admin Dashboard">
    <style>
        @keyframes d-up { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }
        .d { animation: d-up .55s cubic-bezier(.16,1,.3,1) both; }
        .d1 { animation-delay: .00s; } .d2 { animation-delay: .07s; } .d3 { animation-delay: .14s; }
        .d4 { animation-delay: .21s; } .d5 { animation-delay: .28s; } .d6 { animation-delay: .35s; }
        .d7 { animation-delay: .42s; } .d8 { animation-delay: .49s; }
        @keyframes blob-drift {
            0%,100% { transform: translate(0,0) scale(1); }
            33%  { transform: translate(-12px,10px) scale(1.07); }
            66%  { transform: translate(10px,-8px) scale(.95); }
        }
        .blob-a { animation: blob-drift 9s ease-in-out infinite; }
        .blob-b { animation: blob-drift 11s ease-in-out infinite reverse; animation-delay: 2s; }
    </style>

    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="dashboard" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-4 md:space-y-5">

                {{-- ── GREETING HERO ── --}}
                <div class="d d1 relative overflow-hidden rounded-3xl bg-base-100 border border-base-300/30 px-7 py-7 md:px-10 md:py-9">
                    <div class="blob-a absolute -top-16 -right-12 size-64 rounded-full bg-primary/10 blur-3xl pointer-events-none"></div>
                    <div class="blob-b absolute -bottom-10 left-1/3 size-44 rounded-full bg-secondary/10 blur-2xl pointer-events-none"></div>
                    <div class="absolute inset-0 pointer-events-none" style="background-image:radial-gradient(circle,oklch(var(--bc)/.06) 1px,transparent 1px);background-size:22px 22px;"></div>
                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                        <div class="flex items-center gap-4">
                            @if ($user->avatarUrl)
                                <img src="{{ $user->avatarUrl }}" class="size-14 rounded-2xl object-cover ring-2 ring-base-300/50 shrink-0" alt="">
                            @else
                                <div class="size-14 rounded-2xl bg-primary/10 flex items-center justify-center text-xl font-black text-primary shrink-0">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            @endif
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Welcome back</p>
                                <h1 class="mt-0.5 text-3xl md:text-4xl font-black tracking-tight">{{ $user->name }}</h1>
                                <p class="mt-1.5 text-sm text-base-content/40">{{ now()->format('l, F j') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            @if ($pendingExcuses > 0)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-warning/10 border border-warning/25 text-warning text-sm font-semibold px-3.5 py-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M10.29 3.86L1.71 18a2 2 0 0 0 1.71 3h16.58a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0ZM12 9v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                    {{ $pendingExcuses }} pending
                                </span>
                            @endif
                            <span class="inline-flex items-center rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold px-4 py-1.5">Admin</span>
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

                {{-- ── QUICK ACTIONS ── --}}
                <div class="d d8 rounded-2xl border border-base-300/40 bg-base-100 p-5">
                    <p class="text-xs uppercase tracking-widest text-base-content/35 font-semibold mb-3">Quick Actions</p>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.users.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary/10 text-primary border border-primary/15 text-sm font-medium hover:bg-primary/15 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 4v6m3-3h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Manage Users
                        </a>
                        <a href="{{ route('admin.settings.edit') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-base-200 text-base-content/70 border border-base-300/50 text-sm font-medium hover:bg-base-300/50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
                            Site Settings
                        </a>
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
