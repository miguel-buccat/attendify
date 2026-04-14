<x-layouts.app title="Attendance History">
    <style>
        @keyframes d-up { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .d { animation: d-up .45s cubic-bezier(.16,1,.3,1) both; }
        .d1 { animation-delay: .00s; } .d2 { animation-delay: .07s; } .d3 { animation-delay: .14s; }
        .d4 { animation-delay: .21s; } .d5 { animation-delay: .28s; } .d6 { animation-delay: .35s; }
    </style>
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="attendance" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                <div class="d d1">
                    <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Student</p>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight">Attendance History</h1>
                    <p class="mt-1 text-sm text-base-content/50">Your attendance records across all classes.</p>
                </div>

                @if ($records->isEmpty())
                    <div class="d d2 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                        <div class="py-10 flex flex-col items-center gap-2 text-center px-6">
                            <div class="size-14 rounded-2xl bg-base-200 flex items-center justify-center mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-7 text-base-content/30" aria-hidden="true">
                                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <p class="font-semibold text-base-content/60">No attendance records yet</p>
                            <p class="text-sm text-base-content/40">Scan a teacher's QR code to record your first attendance.</p>
                        </div>
                    </div>
                @else
                    <div class="d d2 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                        <div class="px-5 py-4 border-b border-base-300/30 flex items-center justify-between">
                            <h2 class="font-semibold text-sm">Records</h2>
                            <span class="text-xs text-base-content/40">{{ $records->total() }} total</span>
                        </div>
                        <div class="divide-y divide-base-300/30">
                            @foreach ($records as $record)
                                @php
                                    $statusPill = match ($record->status->value) {
                                        'Present' => 'text-success bg-success/10 border-success/20',
                                        'Late'    => 'text-warning bg-warning/10 border-warning/20',
                                        'Absent'  => 'text-error bg-error/10 border-error/20',
                                        'Excused' => 'text-info bg-info/10 border-info/20',
                                        default   => 'text-base-content/40 bg-base-200 border-base-300/50',
                                    };
                                @endphp
                                <div class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-base-200/40 transition-colors">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium truncate">{{ $record->classSession->schoolClass->name }}</p>
                                        <p class="text-xs text-base-content/40 mt-0.5">
                                            {{ $record->classSession->start_time->format('M d, Y') }}
                                            @if ($record->scanned_at)
                                                · {{ $record->scanned_at->format('g:i A') }}
                                            @endif
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold shrink-0 {{ $statusPill }}">{{ $record->status->value }}</span>
                                </div>
                            @endforeach
                        </div>
                        @if ($records->hasPages())
                            <div class="px-5 py-4 border-t border-base-300/30">
                                {{ $records->links() }}
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </main>
    </div>
</x-layouts.app>
