<x-layouts.app :title="$class->name">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                {{-- Back link + header --}}
                <div>
                    <a href="{{ route('student.classes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/60 hover:text-base-content transition-colors mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                            <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back to Classes
                    </a>
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div class="min-w-0">
                            <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold truncate">{{ $class->name }}</h1>
                            <p class="mt-1 text-sm text-base-content/60">
                                {{ $class->section ? $class->section . ' · ' : '' }}Teacher: {{ $class->teacher->name }}
                            </p>
                        </div>
                        <span class="badge {{ $class->isActive() ? 'badge-success' : 'badge-ghost' }} badge-sm shrink-0 self-start">
                            {{ $class->status->value }}
                        </span>
                    </div>
                </div>

                {{-- Stat cards --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4">
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
                        label="Rate"
                        :value="$attendanceRate . '%'"
                        color="primary"
                        icon='<path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
                    />
                </div>

                {{-- Charts --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-dashboard.chart-card
                        title="Attendance Over Time"
                        chart-type="line"
                        :chart-data="$lineData"
                        canvas-id="class-line-chart"
                    />
                    <x-dashboard.chart-card
                        title="Status Breakdown"
                        chart-type="pie"
                        :chart-data="$pieData"
                        canvas-id="class-pie-chart"
                    />
                </div>

                {{-- Sessions list --}}
                <div class="card bg-base-100 rounded-xl border border-base-300">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-lg">Sessions</h2>

                        @if ($sessions->isEmpty())
                            <p class="text-base-content/50 text-sm">No sessions yet.</p>
                        @else
                            {{-- Mobile: card layout --}}
                            <div class="space-y-0 divide-y divide-base-300 sm:hidden">
                                @foreach ($sessions as $session)
                                    @php
                                        $record = $records->get($session->id);
                                        $statusBadge = match ($record?->status->value ?? null) {
                                            'Present' => 'badge-success',
                                            'Late' => 'badge-warning',
                                            'Absent' => 'badge-error',
                                            'Excused' => 'badge-info',
                                            default => 'badge-ghost',
                                        };
                                        $sessionBadge = match ($session->status->value) {
                                            'Active' => 'badge-success',
                                            'Completed' => 'badge-primary',
                                            'Cancelled' => 'badge-error',
                                            default => 'badge-ghost',
                                        };
                                    @endphp
                                    <div class="px-0 py-3 space-y-1.5">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-sm font-medium">{{ $session->start_time->format('M d, Y') }}</p>
                                            <span class="badge {{ $sessionBadge }} badge-sm shrink-0">{{ $session->status->value }}</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-xs text-base-content/50">
                                                {{ $session->start_time->format('g:i A') }} – {{ $session->end_time->format('g:i A') }}
                                                · {{ $session->modality->value }}
                                            </p>
                                            @if ($record)
                                                <span class="badge {{ $statusBadge }} badge-sm shrink-0">{{ $record->status->value }}</span>
                                            @elseif ($session->status->value === 'Completed')
                                                <span class="badge badge-ghost badge-sm shrink-0">—</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Desktop: table layout --}}
                            <div class="hidden sm:block overflow-x-auto">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Modality</th>
                                            <th>Session Status</th>
                                            <th>My Attendance</th>
                                            <th>Scanned At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sessions as $session)
                                            @php
                                                $record = $records->get($session->id);
                                                $statusBadge = match ($record?->status->value ?? null) {
                                                    'Present' => 'badge-success',
                                                    'Late' => 'badge-warning',
                                                    'Absent' => 'badge-error',
                                                    'Excused' => 'badge-info',
                                                    default => 'badge-ghost',
                                                };
                                                $sessionBadge = match ($session->status->value) {
                                                    'Active' => 'badge-success',
                                                    'Completed' => 'badge-primary',
                                                    'Cancelled' => 'badge-error',
                                                    default => 'badge-ghost',
                                                };
                                            @endphp
                                            <tr>
                                                <td class="font-medium">{{ $session->start_time->format('M d, Y') }}</td>
                                                <td class="text-base-content/60">{{ $session->start_time->format('g:i A') }} – {{ $session->end_time->format('g:i A') }}</td>
                                                <td class="text-base-content/60">{{ $session->modality->value }}</td>
                                                <td><span class="badge {{ $sessionBadge }} badge-sm">{{ $session->status->value }}</span></td>
                                                <td>
                                                    @if ($record)
                                                        <span class="badge {{ $statusBadge }} badge-sm">{{ $record->status->value }}</span>
                                                    @elseif ($session->status->value === 'Completed')
                                                        <span class="badge badge-ghost badge-sm">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-base-content/60">{{ $record?->scanned_at?->format('g:i A') ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </main>
    </div>

    @vite('resources/js/charts.js')
</x-layouts.app>
