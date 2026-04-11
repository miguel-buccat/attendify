<x-layouts.app :title="$session->schoolClass->name . ' — Attendance'">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                {{-- Back link + header --}}
                <div>
                    <a href="{{ route('teacher.sessions.show', $session) }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/60 hover:text-base-content transition-colors mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                            <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back to Session
                    </a>
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div class="min-w-0">
                            <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold truncate">Attendance — {{ $session->schoolClass->name }}</h1>
                            <p class="mt-1 text-sm text-base-content/60">
                                {{ $session->start_time->format('M d, Y g:i A') }} – {{ $session->end_time->format('g:i A') }}
                            </p>
                        </div>
                        <a href="{{ route('teacher.attendance.export', $session) }}" class="btn btn-ghost btn-sm rounded-lg gap-1.5 shrink-0 self-start">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m4-5 5 5 5-5m-5 5V3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Export CSV
                        </a>
                    </div>
                </div>

                {{-- Attendance roster --}}
                <div class="card bg-base-100 rounded-xl border border-base-300">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-lg">Attendance Roster</h2>
                        <p class="text-sm text-base-content/60">{{ $enrolledStudents->count() }} enrolled students</p>

                        {{-- Mobile: card layout --}}
                        <div class="space-y-3 sm:hidden">
                            @foreach ($enrolledStudents as $student)
                                @php $record = $recordsByStudent->get($student->id); @endphp
                                <div class="rounded-xl border border-base-300 bg-base-200/30 p-4 space-y-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="font-medium truncate">{{ $student->name }}</p>
                                            <p class="text-xs text-base-content/50 mt-0.5">{{ $record?->scanned_at?->format('g:i A') ?? 'Not scanned' }}</p>
                                        </div>
                                        @if ($record)
                                            @php
                                                $statusBadge = match ($record->status->value) {
                                                    'Present' => 'badge-success',
                                                    'Late' => 'badge-warning',
                                                    'Absent' => 'badge-error',
                                                    'Excused' => 'badge-info',
                                                    default => 'badge-ghost',
                                                };
                                            @endphp
                                            <span class="badge {{ $statusBadge }} badge-sm shrink-0">{{ $record->status->value }}</span>
                                        @else
                                            <span class="badge badge-ghost badge-sm shrink-0">No Record</span>
                                        @endif
                                    </div>

                                    @if ($record)
                                        <form method="POST" action="{{ route('teacher.attendance.update', $record) }}" class="space-y-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="select select-bordered select-sm w-full rounded-lg">
                                                @foreach (\App\Enums\AttendanceStatus::cases() as $status)
                                                    <option value="{{ $status->value }}" @selected($record->status === $status)>{{ $status->value }}</option>
                                                @endforeach
                                            </select>
                                            <textarea name="notes" class="textarea textarea-bordered textarea-sm w-full rounded-lg" rows="2" placeholder="Notes (optional)" maxlength="500">{{ $record->notes }}</textarea>
                                            <button type="submit" class="btn btn-primary btn-sm btn-block rounded-lg">Save</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Desktop: table layout --}}
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Scanned At</th>
                                        <th>Marked By</th>
                                        <th>Notes</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($enrolledStudents as $student)
                                        @php $record = $recordsByStudent->get($student->id); @endphp
                                        <tr>
                                            <td class="font-medium">{{ $student->name }}</td>
                                            <td class="text-base-content/60 text-sm">{{ $student->email }}</td>
                                            @if ($record)
                                                <td>
                                                    <form method="POST" action="{{ route('teacher.attendance.update', $record) }}" class="flex items-center gap-2" id="form-{{ $record->id }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="status" class="select select-bordered select-xs rounded-lg w-28">
                                                            @foreach (\App\Enums\AttendanceStatus::cases() as $status)
                                                                <option value="{{ $status->value }}" @selected($record->status === $status)>{{ $status->value }}</option>
                                                            @endforeach
                                                        </select>
                                                </td>
                                                <td class="text-base-content/60 text-sm">{{ $record->scanned_at?->format('g:i A') ?? '—' }}</td>
                                                <td class="text-base-content/60 text-sm">{{ $record->marked_by->value }}</td>
                                                <td>
                                                    <input type="text" name="notes" value="{{ $record->notes }}" class="input input-bordered input-xs rounded-lg w-40" placeholder="Notes..." maxlength="500">
                                                </td>
                                                <td>
                                                    <button type="submit" class="btn btn-primary btn-xs rounded-lg">Save</button>
                                                    </form>
                                                </td>
                                            @else
                                                <td><span class="badge badge-ghost badge-sm">No Record</span></td>
                                                <td class="text-base-content/60">—</td>
                                                <td class="text-base-content/60">—</td>
                                                <td>—</td>
                                                <td></td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</x-layouts.app>
