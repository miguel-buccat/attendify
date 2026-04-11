<x-layouts.app title="Attendance History">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="attendance" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold">Attendance History</h1>
                    <p class="mt-1 text-sm sm:text-base text-base-content/60">Your attendance records across all classes.</p>
                </div>

                @if ($records->isEmpty())
                    <div class="card bg-base-100 rounded-xl border border-base-300">
                        <div class="card-body items-center text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-12 text-base-content/20 mb-2" aria-hidden="true">
                                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p class="text-base-content/50 font-medium">No attendance records yet.</p>
                            <p class="text-sm text-base-content/40 mt-1">Scan a teacher's QR code to record your first attendance.</p>
                        </div>
                    </div>
                @else
                    <div class="card bg-base-100 rounded-xl border border-base-300">
                        <div class="card-body gap-0 p-0">

                            {{-- Mobile: card layout --}}
                            <div class="space-y-0 divide-y divide-base-300 sm:hidden">
                                @foreach ($records as $record)
                                    @php
                                        $statusBadge = match ($record->status->value) {
                                            'Present' => 'badge-success',
                                            'Late' => 'badge-warning',
                                            'Absent' => 'badge-error',
                                            'Excused' => 'badge-info',
                                            default => 'badge-ghost',
                                        };
                                    @endphp
                                    <div class="flex items-center justify-between gap-3 px-4 py-3">
                                        <div class="min-w-0">
                                            <p class="font-medium text-sm truncate">{{ $record->classSession->schoolClass->name }}</p>
                                            <p class="text-xs text-base-content/50 mt-0.5">
                                                {{ $record->classSession->start_time->format('M d, Y') }}
                                                @if ($record->scanned_at)
                                                    · {{ $record->scanned_at->format('g:i A') }}
                                                @endif
                                            </p>
                                        </div>
                                        <span class="badge {{ $statusBadge }} badge-sm shrink-0">{{ $record->status->value }}</span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Desktop: table layout --}}
                            <div class="hidden sm:block overflow-x-auto">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Class</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Scanned At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($records as $record)
                                            <tr>
                                                <td class="font-medium">{{ $record->classSession->schoolClass->name }}</td>
                                                <td class="text-base-content/60">{{ $record->classSession->start_time->format('M d, Y') }}</td>
                                                <td>
                                                    @php
                                                        $statusBadge = match ($record->status->value) {
                                                            'Present' => 'badge-success',
                                                            'Late' => 'badge-warning',
                                                            'Absent' => 'badge-error',
                                                            'Excused' => 'badge-info',
                                                            default => 'badge-ghost',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $statusBadge }} badge-sm">{{ $record->status->value }}</span>
                                                </td>
                                                <td class="text-base-content/60">
                                                    {{ $record->scanned_at ? $record->scanned_at->format('g:i A') : '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </main>
    </div>
</x-layouts.app>
