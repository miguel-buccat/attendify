<x-layouts.app title="Admin Dashboard">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="dashboard" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    <div>
                        <p class="text-sm text-base-content/50">Welcome back,</p>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">{{ $user->name }}</h1>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($pendingExcuses > 0)
                            <span class="badge badge-warning gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M12 9v4m0 4h.01M10.29 3.86l-8.58 14.57A2 2 0 0 0 3.43 21h17.14a2 2 0 0 0 1.72-2.57L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                {{ $pendingExcuses }} pending excuse{{ $pendingExcuses > 1 ? 's' : '' }}
                            </span>
                        @endif
                        <span class="badge badge-primary badge-lg">Admin</span>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
                    <x-dashboard.stat-card
                        label="Total Users"
                        :value="$totalUsers"
                        color="primary"
                        :href="route('admin.users.index')"
                        icon='<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 4v6m3-3h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
                    />
                    <x-dashboard.stat-card
                        label="Active Classes"
                        :value="$activeClasses"
                        color="success"
                        icon='<path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>'
                    />
                    <x-dashboard.stat-card
                        label="Sessions This Month"
                        :value="$sessionsThisMonth"
                        color="info"
                        icon='<rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>'
                    />
                    <x-dashboard.stat-card
                        label="Avg Attendance Rate"
                        :value="$avgAttendanceRate . '%'"
                        color="warning"
                        icon='<path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
                    />
                </div>

                {{-- Quick Actions --}}
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline rounded-xl gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 4v6m3-3h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Manage Users
                    </a>
                    <a href="{{ route('admin.settings.edit') }}" class="btn btn-sm btn-outline rounded-xl gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
                        Site Settings
                    </a>
                </div>

                {{-- Charts --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-dashboard.chart-card
                        title="Attendance Distribution"
                        chart-type="pie"
                        :chart-data="$pieData"
                        canvas-id="admin-pie-chart"
                    />
                    <x-dashboard.chart-card
                        title="Attendance Trend (30 Days)"
                        chart-type="line"
                        :chart-data="$lineData"
                        canvas-id="admin-line-chart"
                    />
                </div>
            </div>
        </main>
    </div>

    @vite('resources/js/charts.js')
</x-layouts.app>
