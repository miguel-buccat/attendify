<x-layouts.app title="Attendance History">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="attendance" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-6">

                <div class="d d1">
                    <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Student</p>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight">Attendance History</h1>
                    <p class="mt-1 text-sm text-base-content/50">Your attendance records across all classes.</p>
                </div>

                @if ($records->isEmpty())
                    <div class="d d2">
                        <x-ui.empty-state
                            icon="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2"
                            title="No attendance records yet"
                            description="Scan a teacher's QR code to record your first attendance."
                        />
                    </div>
                @else
                    <div class="d d2 af-card overflow-hidden !p-0">
                        <div class="px-5 py-4 border-b af-divider flex items-center justify-between">
                            <h2 class="font-semibold text-sm">Records</h2>
                            <span class="text-xs text-base-content/40">{{ $records->total() }} total</span>
                        </div>
                        <div class="divide-y af-divider">
                            @foreach ($records as $record)
                                @php
                                    $statusVariant = match ($record->status->value) {
                                        'Present' => 'success',
                                        'Late'    => 'warning',
                                        'Absent'  => 'error',
                                        'Excused' => 'info',
                                        default   => 'neutral',
                                    };
                                @endphp
                                <div class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-base-content/[.03] transition-colors">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold truncate">{{ $record->classSession->schoolClass->name }}</p>
                                        <p class="text-xs text-base-content/40 mt-0.5">
                                            {{ $record->classSession->start_time->format('M d, Y') }}
                                            @if ($record->scanned_at)
                                                · {{ $record->scanned_at->format('g:i A') }}
                                            @endif
                                        </p>
                                    </div>
                                    <x-ui.badge :variant="$statusVariant" size="xs">{{ $record->status->value }}</x-ui.badge>
                                </div>
                            @endforeach
                        </div>
                        @if ($records->hasPages())
                            <div class="px-5 py-4 border-t af-divider">
                                {{ $records->links() }}
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </main>
    </div>
</x-layouts.app>
