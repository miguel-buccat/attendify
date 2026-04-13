<x-layouts.app :title="$session->schoolClass->name . ' — Session'">
    <style>
        @keyframes d-up { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .d { animation: d-up .45s cubic-bezier(.16,1,.3,1) both; }
        .d1 { animation-delay: .00s; } .d2 { animation-delay: .07s; } .d3 { animation-delay: .14s; }
        .d4 { animation-delay: .21s; } .d5 { animation-delay: .28s; }
    </style>

    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-5">

                {{-- Header --}}
                <div class="d d1">
                    <a href="{{ route('teacher.classes.show', $session->schoolClass) }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/40 hover:text-base-content transition-colors mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ $session->schoolClass->name }}
                    </a>
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Session</p>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight">{{ $session->start_time->format('M d, Y') }}</h1>
                            <p class="mt-1 text-sm text-base-content/50">{{ $session->start_time->format('g:i A') }} – {{ $session->end_time->format('g:i A') }}</p>
                        </div>
                        @php
                            $statusStyle = match ($session->status->value) {
                                'Active'    => 'text-success bg-success/10 border-success/20',
                                'Scheduled' => 'text-info bg-info/10 border-info/20',
                                'Completed' => 'text-base-content/50 bg-base-200 border-base-300/50',
                                'Cancelled' => 'text-error bg-error/10 border-error/20',
                                default     => 'text-base-content/50 bg-base-200 border-base-300/50',
                            };
                        @endphp
                        <span id="session-status-badge" class="inline-flex items-center rounded-full border px-3 py-1.5 text-sm font-semibold shrink-0 self-start {{ $statusStyle }}">{{ $session->status->value }}</span>
                    </div>
                </div>

                {{-- Session details card --}}
                <div class="d d2 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-base-300/30">
                        <h2 class="font-semibold text-sm">Session Details</h2>
                    </div>
                    <div class="p-5 space-y-5">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/35">Modality</p>
                                <p class="mt-1 font-semibold text-sm">{{ $session->modality->value }}</p>
                            </div>
                            @if ($session->location)
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/35">Location</p>
                                <p class="mt-1 font-semibold text-sm break-words">{{ $session->location }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/35">Grace Period</p>
                                <p class="mt-1 font-semibold text-sm">{{ $session->grace_period_minutes }} min</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/35">Scanned</p>
                                <p class="mt-1 font-semibold text-sm" id="scanned-count">{{ $scannedCount }} / {{ $enrolledCount }}</p>
                            </div>
                        </div>

                        <div id="session-actions" class="flex flex-wrap gap-2">
                            @if ($session->isScheduled())
                                <form method="POST" action="{{ route('teacher.sessions.start', $session) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm rounded-xl">Start Session</button>
                                </form>
                                <button type="button" onclick="document.getElementById('cancel-session-modal').showModal()" class="btn btn-ghost btn-sm rounded-xl text-error">Cancel</button>
                                @if ($session->isRecurring())
                                    <button type="button" onclick="document.getElementById('cancel-upcoming-modal').showModal()" class="btn btn-ghost btn-sm rounded-xl text-error/70">Cancel All Upcoming</button>
                                @endif
                            @elseif ($session->isActive())
                                <button type="button" onclick="document.getElementById('complete-session-modal').showModal()" class="btn btn-warning btn-sm rounded-xl">Complete Session</button>
                                <button type="button" onclick="document.getElementById('cancel-session-modal').showModal()" class="btn btn-ghost btn-sm rounded-xl text-error">Cancel</button>
                            @endif
                            <a href="{{ route('teacher.attendance.index', $session) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-base-200 text-base-content/60 border border-base-300/50 text-xs font-medium hover:bg-base-300/50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m-6 9 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Manage Attendance
                            </a>
                        </div>

                        @if ($session->cancellation_reason)
                            <div class="mt-2 rounded-xl bg-error/5 border border-error/15 p-4">
                                <p class="text-xs font-bold uppercase tracking-widest text-error/60 mb-1">Cancellation Reason</p>
                                <p class="text-sm text-base-content/70">{{ $session->cancellation_reason }}</p>
                            </div>
                        @endif

                        @if ($session->isRecurring())
                            <div class="mt-2 text-xs text-base-content/40">
                                <span class="font-semibold">Recurring:</span> {{ ucfirst($session->recurrence_pattern) }} until {{ $session->recurrence_end_date?->format('M d, Y') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- QR Code (active only) --}}
                @if ($session->isActive())
                    <div id="qr-section" class="d d3 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                        <div class="px-5 py-4 border-b border-base-300/30">
                            <h2 class="font-semibold text-sm">QR Code — Scan to Attend</h2>
                        </div>
                        <div class="p-5 flex flex-col items-center gap-4">
                            <x-qr-display
                                :payload="route('attend.show', [$session->id, $session->qr_token])"
                                class="w-48 sm:w-64 md:w-72"
                            />
                            <p class="text-sm text-base-content/50 text-center max-w-sm">Show this QR code to your students. They can scan it with their device camera.</p>
                        </div>
                    </div>
                @endif

                {{-- Attendance Progress --}}
                <div id="attendance-section" class="d d4">
                @if ($session->attendanceRecords->isNotEmpty())
                    <div class="rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                        <div class="px-5 py-4 border-b border-base-300/30">
                            <h2 class="font-semibold text-sm">Attendance Progress</h2>
                        </div>
                        <div class="divide-y divide-base-300/30">
                            @foreach ($session->attendanceRecords as $record)
                                @php
                                    $rStyle = match ($record->status->value) {
                                        'Present' => 'text-success bg-success/10 border-success/20',
                                        'Late'    => 'text-warning bg-warning/10 border-warning/20',
                                        'Absent'  => 'text-error bg-error/10 border-error/20',
                                        'Excused' => 'text-info bg-info/10 border-info/20',
                                        default   => 'text-base-content/50 bg-base-200 border-base-300/50',
                                    };
                                @endphp
                                <div class="flex items-center justify-between gap-3 px-5 py-3">
                                    <p class="text-sm font-medium">{{ $record->student->name }}</p>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <span class="text-xs text-base-content/40">{{ $record->scanned_at ? $record->scanned_at->format('g:i A') : '—' }}</span>
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $rStyle }}">{{ $record->status->value }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="rounded-2xl border border-base-300/50 bg-base-100 py-10 flex flex-col items-center gap-2 text-center px-6">
                        <p class="text-sm text-base-content/40">No attendance records yet.</p>
                    </div>
                @endif
                </div>

            </div>
        </main>
    </div>

    {{-- Cancel Session Modal --}}
    @if ($session->isScheduled() || $session->isActive())
    <dialog id="cancel-session-modal" class="modal">
        <div class="modal-box rounded-2xl">
            <h3 class="text-lg font-semibold mb-4">Cancel Session</h3>
            <form method="POST" action="{{ route('teacher.sessions.cancel', $session) }}">
                @csrf
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Reason (optional)</span></label>
                    <textarea name="cancellation_reason" rows="3" class="textarea textarea-bordered rounded-xl w-full" placeholder="Why is this session being cancelled?"></textarea>
                </div>
                <div class="modal-action">
                    <button type="button" onclick="this.closest('dialog').close()" class="btn btn-ghost rounded-xl">Keep Session</button>
                    <button type="submit" class="btn btn-error rounded-xl">Cancel Session</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- Cancel All Upcoming Modal --}}
    @if ($session->isRecurring())
    <dialog id="cancel-upcoming-modal" class="modal">
        <div class="modal-box rounded-2xl max-w-sm">
            <h3 class="text-lg font-bold">Cancel All Upcoming</h3>
            <p class="text-sm text-base-content/60 mt-2">This will cancel all future sessions in this recurring group. This action cannot be undone.</p>
            <div class="modal-action">
                <button type="button" onclick="this.closest('dialog').close()" class="btn btn-ghost rounded-xl">Keep Sessions</button>
                <form method="POST" action="{{ route('teacher.sessions.cancel-upcoming', $session) }}">
                    @csrf
                    <button type="submit" class="btn btn-error rounded-xl">Cancel All</button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
    @endif

    {{-- Complete Session Modal --}}
    <dialog id="complete-session-modal" class="modal">
        <div class="modal-box rounded-2xl max-w-sm">
            <h3 class="text-lg font-bold">Complete Session</h3>
            <p class="text-sm text-base-content/60 mt-2">Students who have not scanned will be marked absent. Are you sure you want to complete this session?</p>
            <div class="modal-action">
                <button type="button" onclick="this.closest('dialog').close()" class="btn btn-ghost rounded-xl">Cancel</button>
                <form method="POST" action="{{ route('teacher.sessions.complete', $session) }}">
                    @csrf
                    <button type="submit" class="btn btn-warning rounded-xl">Complete Session</button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
    <script>
        (function () {
            const url = @json(route('teacher.sessions.attendance', $session));
            const enrolledCount = @json($enrolledCount);
            let polling = null;
            let settled = false;

            function badgeClass(status) {
                const map = {
                    Present: 'text-success bg-success/10 border-success/20',
                    Late:    'text-warning bg-warning/10 border-warning/20',
                    Absent:  'text-error bg-error/10 border-error/20',
                    Excused: 'text-info bg-info/10 border-info/20',
                };
                return map[status] || 'text-base-content/50 bg-base-200 border-base-300/50';
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function buildRowsHtml(records) {
                if (!records.length) return '';
                return records.map(r => `
                    <div class="flex items-center justify-between gap-3 px-5 py-3">
                        <p class="text-sm font-medium">${escapeHtml(r.student_name)}</p>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs text-base-content/40">${escapeHtml(r.scanned_at || '—')}</span>
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold ${badgeClass(r.status)}">${escapeHtml(r.status)}</span>
                        </div>
                    </div>
                `).join('');
            }

            function updateStatusBadge(status) {
                const badge = document.getElementById('session-status-badge');
                if (!badge) return;
                badge.textContent = status;
                const cls = {
                    Active: 'text-success bg-success/10 border-success/20',
                    Completed: 'text-base-content/50 bg-base-200 border-base-300/50',
                    Cancelled: 'text-error bg-error/10 border-error/20',
                    Scheduled: 'text-info bg-info/10 border-info/20',
                };
                badge.className = 'inline-flex items-center rounded-full border px-3 py-1.5 text-sm font-semibold shrink-0 self-start ' + (cls[status] || cls.Completed);
            }

            function updateAttendance(data) {
                document.getElementById('scanned-count').textContent = data.scanned_count + ' / ' + enrolledCount;

                const section = document.getElementById('attendance-section');
                if (data.records.length) {
                    section.innerHTML = `
                        <div class="rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                            <div class="px-5 py-4 border-b border-base-300/30">
                                <h2 class="font-semibold text-sm">Attendance Progress</h2>
                            </div>
                            <div class="divide-y divide-base-300/30">${buildRowsHtml(data.records)}</div>
                        </div>
                    `;
                } else {
                    section.innerHTML = `
                        <div class="rounded-2xl border border-base-300/50 bg-base-100 py-10 flex flex-col items-center gap-2 text-center px-6">
                            <p class="text-sm text-base-content/40">No attendance records yet.</p>
                        </div>
                    `;
                }
            }

            function refresh() {
                fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.json())
                    .then(data => {
                        updateAttendance(data);

                        if (data.session_status !== 'Active' && !settled) {
                            settled = true;
                            updateStatusBadge(data.session_status);
                            // Keep polling briefly for the job to finish marking absentees
                            setTimeout(() => {
                                fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                                    .then(res => res.json())
                                    .then(final => {
                                        updateAttendance(final);
                                        clearInterval(polling);
                                    })
                                    .catch(() => clearInterval(polling));
                            }, 3000);
                        }
                    })
                    .catch(() => {});
            }

            polling = setInterval(refresh, 5000);
        })();
    </script>
    @elseif ($session->isCompleted())
    {{-- Brief polling after session completion to catch queued absentee-marking job --}}
    <script>
        (function () {
            const url = @json(route('teacher.sessions.attendance', $session));
            const enrolledCount = @json($enrolledCount);
            let lastCount = @json($scannedCount);
            let attempts = 0;
            const maxAttempts = 6;

            function badgeClass(status) {
                const map = { Present: 'badge-success', Late: 'badge-warning', Absent: 'badge-error', Excused: 'badge-info' };
                return map[status] || 'badge-ghost';
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function badgeClass2(status) {
                const map = {
                    Present: 'text-success bg-success/10 border-success/20',
                    Late:    'text-warning bg-warning/10 border-warning/20',
                    Absent:  'text-error bg-error/10 border-error/20',
                    Excused: 'text-info bg-info/10 border-info/20',
                };
                return map[status] || 'text-base-content/50 bg-base-200 border-base-300/50';
            }

            function buildRowsHtml2(records) {
                if (!records.length) return '';
                return records.map(r => `
                    <div class="flex items-center justify-between gap-3 px-5 py-3">
                        <p class="text-sm font-medium">${escapeHtml(r.student_name)}</p>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs text-base-content/40">${escapeHtml(r.scanned_at || '—')}</span>
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold ${badgeClass2(r.status)}">${escapeHtml(r.status)}</span>
                        </div>
                    </div>
                `).join('');
            }

            function refresh() {
                attempts++;
                fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('scanned-count').textContent = data.scanned_count + ' / ' + enrolledCount;

                        const section = document.getElementById('attendance-section');
                        if (data.records.length) {
                            section.innerHTML = `
                                <div class="rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                                    <div class="px-5 py-4 border-b border-base-300/30">
                                        <h2 class="font-semibold text-sm">Attendance Progress</h2>
                                    </div>
                                    <div class="divide-y divide-base-300/30">${buildRowsHtml2(data.records)}</div>
                                </div>
                            `;
                        }

                        // Stop polling once records updated or max attempts reached
                        if (data.scanned_count !== lastCount || attempts >= maxAttempts) {
                            clearInterval(polling);
                        }
                    })
                    .catch(() => { if (attempts >= maxAttempts) clearInterval(polling); });
            }

            const polling = setInterval(refresh, 2000);
        })();
    </script>
    @endif
</x-layouts.app>
