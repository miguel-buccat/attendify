<x-layouts.app title="Teacher Dashboard">
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
                            <a href="{{ route('teacher.excuses.index') }}" class="badge badge-warning gap-1 hover:brightness-110 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                {{ $pendingExcuses }} pending
                            </a>
                        @endif
                        <span class="badge badge-secondary badge-lg">Teacher</span>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
                    <x-dashboard.stat-card
                        label="My Classes"
                        :value="$myClasses"
                        color="primary"
                        :href="route('teacher.classes.index')"
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

                {{-- Quick Actions --}}
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('teacher.classes.index') }}" class="btn btn-sm btn-outline rounded-xl gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        My Classes
                    </a>
                    <a href="{{ route('teacher.excuses.index') }}" class="btn btn-sm btn-outline rounded-xl gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6M16 13H8m8 4H8m2-8H8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Excuse Requests
                    </a>
                </div>

                {{-- Charts --}}
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

                {{-- Recent Sessions --}}
                <div class="rounded-2xl border border-base-300/60 bg-base-100 p-5">
                    <h3 class="font-semibold mb-4">Recent Sessions</h3>
                    @if ($recentSessions->isEmpty())
                        <p class="text-sm text-base-content/50">No sessions yet. Start one from your class page.</p>
                    @else
                        <div class="overflow-x-auto -mx-5">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentSessions as $session)
                                        @php
                                            $statusColor = match ($session->status) {
                                                'Active' => 'badge-success',
                                                'Completed' => 'badge-ghost',
                                                'Cancelled' => 'badge-error',
                                                default => 'badge-info',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="font-medium">{{ $session->class_name }}</td>
                                            <td><span class="badge {{ $statusColor }} badge-sm">{{ $session->status }}</span></td>
                                            <td class="text-base-content/60">{{ \Carbon\Carbon::parse($session->created_at)->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    @vite('resources/js/charts.js')
</x-layouts.app>
