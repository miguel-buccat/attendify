<x-layouts.app :title="$class->name">
    <style>
        @keyframes d-up { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .d { animation: d-up .45s cubic-bezier(.16,1,.3,1) both; }
        .d1 { animation-delay: .00s; } .d2 { animation-delay: .07s; } .d3 { animation-delay: .14s; }
        .d4 { animation-delay: .21s; } .d5 { animation-delay: .28s; } .d6 { animation-delay: .35s; }
    </style>
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
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold shrink-0 self-start {{ $class->isActive() ? 'text-success bg-success/10 border-success/20' : 'text-base-content/40 bg-base-200 border-base-300/50' }}">
                            {{ $class->status->value }}
                        </span>
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
                <div class="d d5 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-base-300/30 flex items-center justify-between">
                        <h2 class="font-semibold text-sm">Sessions</h2>
                        <span class="text-xs text-base-content/40">{{ $sessions->count() }} total</span>
                    </div>

                    @if ($sessions->isEmpty())
                        <div class="py-10 flex flex-col items-center gap-2 text-center px-6">
                            <p class="text-sm text-base-content/40">No sessions yet.</p>
                        </div>
                    @else
                        <div class="divide-y divide-base-300/30">
                            @foreach ($sessions as $session)
                                @php
                                    $record = $records->get($session->id);
                                    $attendancePill = $record ? match ($record->status->value) {
                                        'Present' => 'text-success bg-success/10 border-success/20',
                                        'Late'    => 'text-warning bg-warning/10 border-warning/20',
                                        'Absent'  => 'text-error bg-error/10 border-error/20',
                                        'Excused' => 'text-info bg-info/10 border-info/20',
                                        default   => 'text-base-content/40 bg-base-200 border-base-300/50',
                                    } : 'text-base-content/40 bg-base-200 border-base-300/50';
                                    $sessionPill = match ($session->status->value) {
                                        'Active'    => 'text-success bg-success/10 border-success/20',
                                        'Completed' => 'text-primary bg-primary/10 border-primary/20',
                                        'Cancelled' => 'text-error bg-error/10 border-error/20',
                                        default     => 'text-base-content/40 bg-base-200 border-base-300/50',
                                    };
                                @endphp
                                <div class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-base-200/40 transition-colors">
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
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $sessionPill }}">{{ $session->status->value }}</span>
                                        @if ($record || $session->isCompleted())
                                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $attendancePill }}">{{ $record ? $record->status->value : '—' }}</span>
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
