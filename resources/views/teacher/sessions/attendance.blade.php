<x-layouts.app :title="$session->schoolClass->name . ' — Attendance'">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                {{-- Header --}}
                <div class="d d1">
                    <a href="{{ route('teacher.sessions.show', $session) }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/40 hover:text-base-content transition-colors mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                            <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back to Session
                    </a>
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">{{ $session->schoolClass->name }}</p>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight">Attendance</h1>
                            <p class="mt-1 text-sm text-base-content/50">{{ $session->start_time->format('M d, Y g:i A') }} – {{ $session->end_time->format('g:i A') }}</p>
                        </div>
                        <div class="flex gap-2 shrink-0 self-start sm:self-auto">
                            <x-ui.button href="{{ route('teacher.attendance.export', $session) }}" variant="ghost" size="sm">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m4-5 5 5 5-5m-5 5V3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Export CSV
                            </x-ui.button>
                            <x-ui.button href="{{ route('teacher.attendance.export-pdf', $session) }}" variant="ghost" size="sm">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M14 2v6h6M12 18v-6M9 15l3 3 3-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Export PDF
                            </x-ui.button>
                        </div>
                    </div>
                </div>

                {{-- Roster card --}}
                <div class="d d2 af-card overflow-hidden !p-0">
                    <div class="px-5 py-4 border-b af-divider flex items-center justify-between">
                        <h2 class="font-semibold text-sm">Attendance Roster</h2>
                        <span class="text-xs text-base-content/40">{{ $enrolledStudents->count() }} students</span>
                    </div>
                    <div class="divide-y divide-base-content/6">
                        @foreach ($enrolledStudents as $student)
                            @php
                                $record = $recordsByStudent->get($student->id);
                                $statusVariant = $record ? match ($record->status->value) {
                                    'Present' => 'success',
                                    'Late'    => 'warning',
                                    'Absent'  => 'error',
                                    'Excused' => 'info',
                                    default   => 'neutral',
                                } : 'neutral';
                            @endphp
                            <div class="px-5 py-3">
                                {{-- Student info + current status --}}
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium">{{ $student->name }}</p>
                                        <p class="text-xs text-base-content/40">{{ $student->email }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        @if ($record?->scanned_at)
                                            <span class="text-xs text-base-content/40">{{ $record->scanned_at->format('g:i A') }}</span>
                                        @endif
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                            <x-ui.badge :variant="$statusVariant" size="xs">{{ $record ? $record->status->value : 'No Record' }}</x-ui.badge>
                                        </span>
                                    </div>
                                </div>

                                @if ($record)
                                    <form method="POST" action="{{ route('teacher.attendance.update', $record) }}" class="flex flex-wrap items-end gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex-1 min-w-[120px]">
                                            <label class="text-[10px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1">Status</label>
                                            <select name="status" class="af-input">
                                                @foreach (\App\Enums\AttendanceStatus::cases() as $status)
                                                    <option value="{{ $status->value }}" @selected($record->status === $status)>{{ $status->value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex-[2] min-w-[160px]">
                                            <label class="text-[10px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1">Notes</label>
                                            <input type="text" name="notes" value="{{ $record->notes }}" class="af-input" placeholder="Notes (optional)" maxlength="500">
                                        </div>
                                        <div class="shrink-0">
                                            <label class="text-[10px] font-bold uppercase tracking-[.2em] text-transparent block mb-1">_</label>
                                            <x-ui.button type="submit" variant="primary" size="sm">Save</x-ui.button>
                                        </div>
                                        @if ($record->marked_by)
                                            <p class="w-full text-xs text-base-content/35">Marked by {{ $record->marked_by->value }}</p>
                                        @endif
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </main>
    </div>
</x-layouts.app>
