<x-layouts.app :title="$class->name">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                {{-- Back link + header --}}
                <div class="d d1">
                    <a href="{{ route('student.classes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/40 hover:text-base-content transition-colors mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                            <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back to Classes
                    </a>
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Student · {{ $class->teacher->name }}</p>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight">{{ $class->name }}</h1>
                            @if ($class->section)
                                <p class="mt-1 text-sm text-base-content/50">{{ $class->section }}</p>
                            @endif
                        </div>
                        @if ($class->isActive())
                            <x-ui.badge variant="success">{{ $class->status->value }}</x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral">{{ $class->status->value }}</x-ui.badge>
                        @endif
                    </div>
                </div>

                {{-- Stat cards --}}
                <div class="d d2 grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4">
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
                <div class="d d3 grid grid-cols-1 lg:grid-cols-2 gap-4">
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
                <div class="d d5 af-card overflow-hidden !p-0">
                    <div class="px-5 py-4 border-b af-divider flex items-center justify-between">
                        <h2 class="font-semibold text-sm">Sessions</h2>
                        <span class="text-xs text-base-content/40">{{ $sessions->count() }} total</span>
                    </div>

                    @if ($sessions->isEmpty())
                        <x-ui.empty-state
                            icon="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                            title="No sessions yet"
                            description="No sessions yet."
                        />
                    @else
                        <div class="divide-y af-divider">
                            @foreach ($sessions as $session)
                                @php
                                    $record = $records->get($session->id);
                                    $attendanceVariant = $record ? match ($record->status->value) {
                                        'Present' => 'success',
                                        'Late'    => 'warning',
                                        'Absent'  => 'error',
                                        'Excused' => 'info',
                                        default   => 'neutral',
                                    } : 'neutral';
                                    $sessionVariant = match ($session->status->value) {
                                        'Active'    => 'success',
                                        'Completed' => 'primary',
                                        'Cancelled' => 'error',
                                        default     => 'neutral',
                                    };
                                @endphp
                                <div class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-base-content/[.03] transition-colors">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium">{{ $session->start_time->format('M d, Y') }}</p>
                                        <p class="text-xs text-base-content/40 mt-0.5">
                                            {{ $session->start_time->format('g:i A') }} – {{ $session->end_time->format('g:i A') }}
                                            · {{ $session->modality->value }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        @if ($record?->scanned_at)
                                            <span class="text-xs text-base-content/40 hidden sm:block">{{ $record->scanned_at->format('g:i A') }}</span>
                                        @endif
                                        <x-ui.badge :variant="$sessionVariant" size="xs">{{ $session->status->value }}</x-ui.badge>
                                        @if ($record || $session->isCompleted())
                                            <x-ui.badge :variant="$attendanceVariant" size="xs">{{ $record ? $record->status->value : '—' }}</x-ui.badge>
                                        @endif
                                    </div>
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
