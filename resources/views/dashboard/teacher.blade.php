<x-layouts.app title="Teacher Dashboard">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="dashboard" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold">Teacher Dashboard</h1>
                        <p class="mt-1 text-sm text-base-content/60">Welcome back, {{ $user->name }}</p>
                    </div>
                    <span class="badge badge-secondary badge-lg hidden sm:inline-flex">Teacher</span>
                </div>

                <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
                    <x-dashboard.stat-card
                        label="My Classes"
                        :value="$myClasses"
                        color="primary"
                        icon='<path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>'
                    />
                    <x-dashboard.stat-card
                        label="Total Students"
                        :value="$totalStudents"
                        color="success"
                        icon='<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
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

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-dashboard.chart-card
                        title="Per-Class Attendance Rate"
                        chart-type="bar"
                        :chart-data="$barData"
                        canvas-id="teacher-bar-chart"
                    />
                    <x-dashboard.chart-card
                        title="Attendance Distribution"
                        chart-type="pie"
                        :chart-data="$pieData"
                        canvas-id="teacher-pie-chart"
                    />
                </div>
            </div>
        </main>
    </div>

    @vite('resources/js/charts.js')
</x-layouts.app>
