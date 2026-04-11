<x-layouts.app title="Student Dashboard">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="dashboard" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold">Student Dashboard</h1>
                        <p class="mt-1 text-sm text-base-content/60">Welcome back, {{ $user->name }}</p>
                    </div>
                    <span class="badge badge-accent badge-lg hidden sm:inline-flex">Student</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
                    <x-dashboard.stat-card
                        label="My Classes"
                        :value="$myClasses"
                        color="primary"
                        icon='<path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>'
                    />
                    <x-dashboard.stat-card
                        label="Present"
                        :value="$presentCount"
                        color="success"
                        icon='<path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
                    />
                    <x-dashboard.stat-card
                        label="Late"
                        :value="$lateCount"
                        color="warning"
                        icon='<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>'
                    />
                    <x-dashboard.stat-card
                        label="Absent"
                        :value="$absentCount"
                        color="error"
                        icon='<path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
                    />
                    <x-dashboard.stat-card
                        label="Excused"
                        :value="$excusedCount"
                        color="info"
                        icon='<path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
                    />
                    <x-dashboard.stat-card
                        label="Attendance Rate"
                        :value="$attendanceRate . '%'"
                        color="secondary"
                        icon='<path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
                    />
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-dashboard.chart-card
                        title="My Attendance Over Time"
                        chart-type="line"
                        :chart-data="$lineData"
                        canvas-id="student-line-chart"
                    />
                    <x-dashboard.chart-card
                        title="Status Breakdown"
                        chart-type="pie"
                        :chart-data="$pieData"
                        canvas-id="student-pie-chart"
                    />
                </div>
            </div>
        </main>
    </div>

    @vite('resources/js/charts.js')
</x-layouts.app>
