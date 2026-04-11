<x-layouts.app :title="$session->schoolClass->name . ' — Session'">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                {{-- Back link + header --}}
                <div>
                    <a href="{{ route('teacher.classes.show', $session->schoolClass) }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/60 hover:text-base-content transition-colors mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                            <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back to {{ $session->schoolClass->name }}
                    </a>
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 sm:gap-4">
                        <div class="min-w-0">
                            <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold truncate">{{ $session->schoolClass->name }} — Session</h1>
                            <p class="mt-1 text-sm sm:text-base text-base-content/60">
                                {{ $session->start_time->format('M d, Y g:i A') }} – {{ $session->end_time->format('g:i A') }}
                            </p>
                        </div>
                        @php
                            $badgeClass = match ($session->status->value) {
                                'Active' => 'badge-success',
                                'Scheduled' => 'badge-info',
                                'Completed' => 'badge-ghost',
                                'Cancelled' => 'badge-error',
                                default => 'badge-ghost',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} badge-lg shrink-0 self-start">{{ $session->status->value }}</span>
                    </div>
                </div>

                {{-- Session details card --}}
                <div class="card bg-base-100 rounded-xl border border-base-300">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-lg">Session Details</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <span class="text-xs font-medium text-base-content/50 uppercase tracking-wide">Modality</span>
                                <p class="mt-0.5 font-medium">{{ $session->modality->value }}</p>
                            </div>
                            @if ($session->location)
                                <div>
                                    <span class="text-xs font-medium text-base-content/50 uppercase tracking-wide">Location</span>
                                    <p class="mt-0.5 font-medium break-words">{{ $session->location }}</p>
                                </div>
                            @endif
                            <div>
                                <span class="text-xs font-medium text-base-content/50 uppercase tracking-wide">Grace Period</span>
                                <p class="mt-0.5 font-medium">{{ $session->grace_period_minutes }} min</p>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-base-content/50 uppercase tracking-wide">Scanned</span>
                                <p class="mt-0.5 font-medium" id="scanned-count">{{ $scannedCount }} / {{ $enrolledCount }}</p>
                            </div>
                        </div>

                        {{-- Action buttons --}}
                        <div class="flex flex-wrap gap-2 mt-2">
                            @if ($session->isScheduled())
                                <form method="POST" action="{{ route('teacher.sessions.start', $session) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm rounded-lg">Start Session</button>
                                </form>
                                <form method="POST" action="{{ route('teacher.sessions.cancel', $session) }}" onsubmit="return confirm('Cancel this session?')">
                                    @csrf
                                    <button type="submit" class="btn btn-ghost btn-sm rounded-lg text-error">Cancel</button>
                                </form>
                            @elseif ($session->isActive())
                                <form method="POST" action="{{ route('teacher.sessions.complete', $session) }}" onsubmit="return confirm('Complete this session? Students who have not scanned will be marked absent.')">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm rounded-lg">Complete Session</button>
                                </form>
                                <form method="POST" action="{{ route('teacher.sessions.cancel', $session) }}" onsubmit="return confirm('Cancel this session?')">
                                    @csrf
                                    <button type="submit" class="btn btn-ghost btn-sm rounded-lg text-error">Cancel</button>
                                </form>
                            @endif
                            <a href="{{ route('teacher.attendance.index', $session) }}" class="btn btn-ghost btn-sm rounded-lg gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m-6 9 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Manage Attendance
                            </a>
                        </div>
                    </div>
                </div>

                {{-- QR Code display (only when active) --}}
                @if ($session->isActive())
                    <div class="card bg-base-100 rounded-xl border border-base-300">
                        <div class="card-body items-center text-center gap-4 px-4 sm:px-6">
                            <h2 class="card-title text-lg">QR Code — Scan to Attend</h2>
                            <x-qr-display
                                :payload="json_encode(['session_id' => $session->id, 'token' => $session->qr_token])"
                                class="w-48 sm:w-64 md:w-80"
                            />
                            <p class="text-sm text-base-content/60 max-w-sm">
                                Show this QR code to your students. They can scan it with their device camera.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Scan progress --}}
                <div id="attendance-section">
                @if ($session->attendanceRecords->isNotEmpty())
                    <div class="card bg-base-100 rounded-xl border border-base-300">
                        <div class="card-body gap-4">
                            <h2 class="card-title text-lg">Attendance Progress</h2>

                            {{-- Mobile: card layout --}}
                            <div class="space-y-2 sm:hidden" id="attendance-mobile">
                                @foreach ($session->attendanceRecords as $record)
                                    @php
                                        $statusBadge = match ($record->status->value) {
                                            'Present' => 'badge-success',
                                            'Late' => 'badge-warning',
                                            'Absent' => 'badge-error',
                                            'Excused' => 'badge-info',
                                            default => 'badge-ghost',
                                        };
                                    @endphp
                                    <div class="flex items-center justify-between gap-3 rounded-xl bg-base-200/40 px-4 py-3">
                                        <div class="min-w-0">
                                            <p class="font-medium truncate">{{ $record->student->name }}</p>
                                            <p class="text-xs text-base-content/50 mt-0.5">
                                                {{ $record->scanned_at ? $record->scanned_at->format('g:i A') : '—' }}
                                            </p>
                                        </div>
                                        <span class="badge {{ $statusBadge }} badge-sm shrink-0">{{ $record->status->value }}</span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Desktop: table layout --}}
                            <div class="hidden sm:block overflow-x-auto" id="attendance-desktop">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Status</th>
                                            <th>Scanned At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($session->attendanceRecords as $record)
                                            <tr>
                                                <td class="font-medium">{{ $record->student->name }}</td>
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
                @else
                    <div class="card bg-base-100 rounded-xl border border-base-300">
                        <div class="card-body items-center text-center py-8">
                            <p class="text-base-content/50">No attendance records yet.</p>
                        </div>
                    </div>
                @endif
                </div>

            </div>
        </main>
    </div>

    @if ($session->isActive())
    <script>
        (function () {
            const url = @json(route('teacher.sessions.attendance', $session));
            const enrolledCount = @json($enrolledCount);
            let polling = null;

            function badgeClass(status) {
                const map = { Present: 'badge-success', Late: 'badge-warning', Absent: 'badge-error', Excused: 'badge-info' };
                return map[status] || 'badge-ghost';
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function buildMobileHtml(records) {
                if (!records.length) return '';
                return records.map(r => `
                    <div class="flex items-center justify-between gap-3 rounded-xl bg-base-200/40 px-4 py-3">
                        <div class="min-w-0">
                            <p class="font-medium truncate">${escapeHtml(r.student_name)}</p>
                            <p class="text-xs text-base-content/50 mt-0.5">${escapeHtml(r.scanned_at || '—')}</p>
                        </div>
                        <span class="badge ${badgeClass(r.status)} badge-sm shrink-0">${escapeHtml(r.status)}</span>
                    </div>
                `).join('');
            }

            function buildDesktopHtml(records) {
                if (!records.length) return '';
                const rows = records.map(r => `
                    <tr>
                        <td class="font-medium">${escapeHtml(r.student_name)}</td>
                        <td><span class="badge ${badgeClass(r.status)} badge-sm">${escapeHtml(r.status)}</span></td>
                        <td class="text-base-content/60">${escapeHtml(r.scanned_at || '—')}</td>
                    </tr>
                `).join('');
                return `<table class="table table-sm"><thead><tr><th>Student</th><th>Status</th><th>Scanned At</th></tr></thead><tbody>${rows}</tbody></table>`;
            }

            function refresh() {
                fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('scanned-count').textContent = data.scanned_count + ' / ' + enrolledCount;

                        const section = document.getElementById('attendance-section');
                        if (data.records.length) {
                            section.innerHTML = `
                                <div class="card bg-base-100 rounded-xl border border-base-300">
                                    <div class="card-body gap-4">
                                        <h2 class="card-title text-lg">Attendance Progress</h2>
                                        <div class="space-y-2 sm:hidden">${buildMobileHtml(data.records)}</div>
                                        <div class="hidden sm:block overflow-x-auto">${buildDesktopHtml(data.records)}</div>
                                    </div>
                                </div>
                            `;
                        } else {
                            section.innerHTML = `
                                <div class="card bg-base-100 rounded-xl border border-base-300">
                                    <div class="card-body items-center text-center py-8">
                                        <p class="text-base-content/50">No attendance records yet.</p>
                                    </div>
                                </div>
                            `;
                        }

                        if (data.session_status !== 'Active') {
                            clearInterval(polling);
                            location.reload();
                        }
                    })
                    .catch(() => {});
            }

            polling = setInterval(refresh, 5000);
        })();
    </script>
    @endif
</x-layouts.app>
