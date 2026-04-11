<x-layouts.app title="Student Dashboard">
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
                    <span class="badge badge-accent badge-lg">Student</span>
                </div>

                {{-- Attendance Rate Hero --}}
                <div class="rounded-2xl border border-base-300/60 bg-base-100 p-5 sm:p-6 flex flex-col sm:flex-row items-center gap-5">
                    <div class="radial-progress text-primary font-bold text-xl" style="--value:{{ $attendanceRate }}; --size:5rem; --thickness:6px;" role="progressbar" aria-valuenow="{{ $attendanceRate }}" aria-valuemin="0" aria-valuemax="100">
                        {{ $attendanceRate }}%
                    </div>
                    <div class="text-center sm:text-left">
                        <h2 class="text-lg font-semibold">Your Attendance Rate</h2>
                        <p class="text-sm text-base-content/50 mt-0.5">Based on all recorded sessions</p>
                    </div>
                    <div class="sm:ml-auto flex flex-wrap justify-center gap-4 text-center">
                        <div>
                            <p class="text-2xl font-bold text-success">{{ $presentCount }}</p>
                            <p class="text-xs text-base-content/50">Present</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-warning">{{ $lateCount }}</p>
                            <p class="text-xs text-base-content/50">Late</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-error">{{ $absentCount }}</p>
                            <p class="text-xs text-base-content/50">Absent</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-info">{{ $excusedCount }}</p>
                            <p class="text-xs text-base-content/50">Excused</p>
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    <x-dashboard.stat-card
                        label="My Classes"
                        :value="$myClasses"
                        color="primary"
                        :href="route('student.classes.index')"
                        icon='<path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>'
                    />
                    <x-dashboard.stat-card
                        label="Attendance Rate"
                        :value="$attendanceRate . '%'"
                        color="secondary"
                        :href="route('student.attendance.index')"
                        icon='<path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
                    />
                </div>

                {{-- Quick Actions --}}
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('student.scan.index') }}" class="btn btn-sm btn-primary rounded-xl gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M3 7V5a2 2 0 0 1 2-2h2m10 0h2a2 2 0 0 1 2 2v2m0 10v2a2 2 0 0 1-2 2h-2M3 17v2a2 2 0 0 0 2 2h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 12h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        Scan QR
                    </a>
                    <a href="{{ route('student.classes.index') }}" class="btn btn-sm btn-outline rounded-xl gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        My Classes
                    </a>
                    <a href="{{ route('student.attendance.index') }}" class="btn btn-sm btn-outline rounded-xl gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Attendance History
                    </a>
                    <a href="{{ route('student.excuses.create') }}" class="btn btn-sm btn-outline rounded-xl gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        File Excuse
                    </a>
                </div>

                {{-- Charts --}}
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

                {{-- Recent Records --}}
                <div class="rounded-2xl border border-base-300/60 bg-base-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold">Recent Attendance</h3>
                        <a href="{{ route('student.attendance.index') }}" class="text-xs text-primary hover:underline">View all</a>
                    </div>
                    @if ($recentRecords->isEmpty())
                        <p class="text-sm text-base-content/50">No attendance records yet.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($recentRecords as $record)
                                @php
                                    $statusBadge = match ($record->status) {
                                        \App\Enums\AttendanceStatus::Present => 'badge-success',
                                        \App\Enums\AttendanceStatus::Late => 'badge-warning',
                                        \App\Enums\AttendanceStatus::Absent => 'badge-error',
                                        \App\Enums\AttendanceStatus::Excused => 'badge-info',
                                        default => 'badge-ghost',
                                    };
                                @endphp
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium truncate">{{ $record->classSession?->schoolClass?->name ?? 'Unknown Class' }}</p>
                                        <p class="text-xs text-base-content/50">{{ $record->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="badge {{ $statusBadge }} badge-sm shrink-0">{{ $record->status->value }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    @vite('resources/js/charts.js')
</x-layouts.app>
