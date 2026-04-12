<x-layouts.app :title="$session->schoolClass->name . ' — Attendance'">
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
                        <a href="{{ route('teacher.attendance.export', $session) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-base-200 text-base-content/60 border border-base-300/50 text-sm font-medium hover:bg-base-300/50 transition-colors shrink-0 self-start sm:self-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m4-5 5 5 5-5m-5 5V3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Export CSV
                        </a>
                    </div>
                </div>

                {{-- Roster card --}}
                <div class="d d2 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-base-300/30 flex items-center justify-between">
                        <h2 class="font-semibold text-sm">Attendance Roster</h2>
                        <span class="text-xs text-base-content/40">{{ $enrolledStudents->count() }} students</span>
                    </div>
                    <div class="divide-y divide-base-300/30">
                        @foreach ($enrolledStudents as $student)
                            @php
                                $record = $recordsByStudent->get($student->id);
                                $statusPill = $record ? match ($record->status->value) {
                                    'Present' => 'text-success bg-success/10 border-success/20',
                                    'Late'    => 'text-warning bg-warning/10 border-warning/20',
                                    'Absent'  => 'text-error bg-error/10 border-error/20',
                                    'Excused' => 'text-info bg-info/10 border-info/20',
                                    default   => 'text-base-content/50 bg-base-200 border-base-300/50',
                                } : 'text-base-content/40 bg-base-200 border-base-300/50';
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
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusPill }}">
                                            {{ $record ? $record->status->value : 'No Record' }}
                                        </span>
                                    </div>
                                </div>

                                @if ($record)
                                    <form method="POST" action="{{ route('teacher.attendance.update', $record) }}" class="flex flex-wrap items-end gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex-1 min-w-[120px]">
                                            <label class="text-[10px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1">Status</label>
                                            <select name="status" class="w-full rounded-xl border border-base-300/70 bg-base-100 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40">
                                                @foreach (\App\Enums\AttendanceStatus::cases() as $status)
                                                    <option value="{{ $status->value }}" @selected($record->status === $status)>{{ $status->value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex-[2] min-w-[160px]">
                                            <label class="text-[10px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1">Notes</label>
                                            <input type="text" name="notes" value="{{ $record->notes }}" class="w-full rounded-xl border border-base-300/70 bg-base-100 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40" placeholder="Notes (optional)" maxlength="500">
                                        </div>
                                        <div class="shrink-0">
                                            <label class="text-[10px] font-bold uppercase tracking-[.2em] text-transparent block mb-1">_</label>
                                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl bg-primary text-primary-content text-sm font-semibold hover:opacity-90 transition-opacity">
                                                Save
                                            </button>
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
