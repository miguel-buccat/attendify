<x-layouts.app title="{{ $student->name }} - {{ $class->name }}">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-5">

                {{-- Breadcrumb --}}
                <div class="text-sm breadcrumbs">
                    <ul>
                        <li><a href="{{ route('teacher.classes.index') }}">My Classes</a></li>
                        <li><a href="{{ route('teacher.classes.show', $class) }}">{{ $class->name }}</a></li>
                        <li>{{ $student->name }}</li>
                    </ul>
                </div>

                {{-- Student Overview --}}
                <div class="af-card p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="flex items-center gap-4">
                            @if ($student->avatarUrl)
                                <img src="{{ $student->avatarUrl }}" class="size-16 rounded-2xl object-cover ring-2 ring-primary/15" alt="">
                            @else
                                <div class="size-16 rounded-2xl bg-accent/10 ring-1 ring-accent/15 flex items-center justify-center text-2xl font-black text-accent">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h1 class="text-2xl font-black tracking-tight">{{ $student->name }}</h1>
                                <p class="text-sm text-base-content/50">{{ $student->email }}</p>
                            </div>
                        </div>
                        <div class="sm:ml-auto text-right">
                            <p class="text-3xl font-black {{ $attendanceRate >= 80 ? 'text-success' : ($attendanceRate >= 60 ? 'text-warning' : 'text-error') }}">
                                {{ $attendanceRate }}%
                            </p>
                            <p class="text-xs text-base-content/40 uppercase font-bold">Attendance Rate</p>
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="af-card p-4 text-center">
                        <p class="text-2xl font-black text-success">{{ $presentCount }}</p>
                        <p class="text-xs text-base-content/50 mt-1">Present</p>
                    </div>
                    <div class="af-card p-4 text-center">
                        <p class="text-2xl font-black text-warning">{{ $lateCount }}</p>
                        <p class="text-xs text-base-content/50 mt-1">Late</p>
                    </div>
                    <div class="af-card p-4 text-center">
                        <p class="text-2xl font-black text-error">{{ $absentCount }}</p>
                        <p class="text-xs text-base-content/50 mt-1">Absent</p>
                    </div>
                    <div class="af-card p-4 text-center">
                        <p class="text-2xl font-black text-info">{{ $excusedCount }}</p>
                        <p class="text-xs text-base-content/50 mt-1">Excused</p>
                    </div>
                </div>

                {{-- Attendance History --}}
                <div class="af-card overflow-hidden !p-0">
                    <div class="p-5 border-b af-divider">
                        <h2 class="text-lg font-bold">Session-by-Session Attendance</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr class="border-b af-divider">
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">Date</th>
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">Time</th>
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">Modality</th>
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">Status</th>
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">Scanned At</th>
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($records as $record)
                                    <tr class="border-b af-divider">
                                        <td class="text-sm">{{ $record->classSession->start_time->format('M d, Y') }}</td>
                                        <td class="text-sm text-base-content/60">{{ $record->classSession->start_time->format('g:i A') }} - {{ $record->classSession->end_time->format('g:i A') }}</td>
                                        <td><x-ui.badge variant="neutral" size="xs">{{ $record->classSession->modality->value }}</x-ui.badge></td>
                                        <td>
                                            @php
                                                $statusVariant = match($record->status->value) {
                                                    'Present' => 'success',
                                                    'Late' => 'warning',
                                                    'Absent' => 'error',
                                                    'Excused' => 'info',
                                                    default => 'neutral',
                                                };
                                            @endphp
                                            <x-ui.badge :variant="$statusVariant" size="xs">{{ $record->status->value }}</x-ui.badge>
                                        </td>
                                        <td class="text-xs text-base-content/60">{{ $record->scanned_at?->format('g:i A') ?? '—' }}</td>
                                        <td class="text-xs text-base-content/60 max-w-32 truncate">{{ $record->notes ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-12 text-base-content/40">No attendance records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
