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
            <div class="p-4 md:p-6 lg:p-8 space-y-5">

                {{-- Header --}}
                <div class="d d1">
                    <a href="{{ route('teacher.classes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/40 hover:text-base-content transition-colors mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        My Classes
                    </a>
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Class</p>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight">{{ $class->name }}</h1>
                            @if ($class->section)
                                <p class="mt-1 text-sm text-base-content/50">{{ $class->section }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0 flex-wrap">
                            @if ($class->status->value === 'Active')
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold text-success bg-success/10 border-success/20">Active</span>
                            @else
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold text-base-content/50 bg-base-200 border-base-300/50">Archived</span>
                            @endif
                            @if ($class->isActive())
                                <button type="button" onclick="document.getElementById('archive-class-modal').showModal()" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-base-200 text-base-content/50 border border-base-300/50 text-xs font-medium hover:bg-base-300/50 transition-colors">Archive</button>
                            @endif
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="d d2">
                        <x-alert type="success" :message="session('success')" />
                    </div>
                @endif

                {{-- Edit details (collapsible) --}}
                @if ($class->description || $class->isActive())
                <div class="d d2 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                    <details class="group">
                        <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer hover:bg-base-200/40 transition-colors list-none">
                            <span class="font-semibold text-sm">Class Details</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 text-base-content/40 transition-transform group-open:rotate-180"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </summary>
                        <div class="px-5 pb-5 pt-2 space-y-4 border-t border-base-300/30">
                            @if ($class->description)
                                <p class="text-sm text-base-content/60">{{ $class->description }}</p>
                            @endif
                            @if ($class->isActive())
                            <form method="POST" action="{{ route('teacher.classes.update', $class) }}" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="form-control">
                                        <label class="label pb-1" for="edit-name"><span class="label-text text-sm">Name</span></label>
                                        <input id="edit-name" type="text" name="name" value="{{ old('name', $class->name) }}" class="input input-bordered w-full rounded-xl input-sm h-10 @error('name') input-error @enderror" required>
                                        @error('name') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="form-control">
                                        <label class="label pb-1" for="edit-section"><span class="label-text text-sm">Section</span></label>
                                        <input id="edit-section" type="text" name="section" value="{{ old('section', $class->section) }}" class="input input-bordered w-full rounded-xl input-sm h-10">
                                    </div>
                                </div>
                                <div class="form-control">
                                    <label class="label pb-1" for="edit-desc"><span class="label-text text-sm">Description</span></label>
                                    <textarea id="edit-desc" name="description" rows="2" class="textarea textarea-bordered w-full rounded-xl text-sm">{{ old('description', $class->description) }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm rounded-xl">Save Changes</button>
                            </form>
                            @endif
                        </div>
                    </details>
                </div>
                @endif

                {{-- Enroll Students --}}
                @if ($class->isActive())
                <div class="d d3 rounded-2xl border border-base-300/50 bg-base-100">
                    <div class="px-5 py-4 border-b border-base-300/30">
                        <h2 class="font-semibold text-sm">Enroll Students</h2>
                        <p class="text-xs text-base-content/40 mt-0.5">Search by name or email to add students to this class.</p>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="relative" id="search-container">
                            <input type="text" id="student-search"
                                class="input input-bordered w-full rounded-xl input-sm h-10"
                                placeholder="Type a student name or email…"
                                autocomplete="off" name="student_search_nonce" role="presentation">
                            <div id="search-results" class="absolute z-20 top-full left-0 right-0 mt-1 rounded-2xl border border-base-300/50 bg-base-100 shadow-xl hidden max-h-64 overflow-y-auto"></div>
                        </div>
                        <form method="POST" action="{{ route('teacher.classes.enroll', $class) }}" id="enroll-form">
                            @csrf
                            <div id="selected-students" class="space-y-2"></div>
                            <button type="submit" id="enroll-btn" class="btn btn-primary btn-sm rounded-xl mt-3 hidden">Enroll Students</button>
                        </form>
                    </div>
                </div>
                @endif

                {{-- Student Roster --}}
                <div class="d d4 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-base-300/30 flex items-center justify-between">
                        <h2 class="font-semibold text-sm">Students</h2>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('teacher.classes.analytics.pdf', $class) }}" class="inline-flex items-center gap-1.5 text-xs text-primary hover:underline">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m4-5 5 5 5-5m-5 5V3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Export PDF
                            </a>
                            <span class="text-xs text-base-content/40 font-mono">{{ $class->students->count() }}</span>
                        </div>
                    </div>
                    @if ($class->students->isEmpty())
                        <div class="py-10 flex flex-col items-center gap-2 text-center px-6">
                            <p class="text-sm text-base-content/40">No students enrolled yet.</p>
                        </div>
                    @else
                        <div class="divide-y divide-base-300/30">
                            @foreach ($class->students as $student)
                                <div class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-base-200/40 transition-colors">
                                    <a href="{{ route('profile.show', $student) }}" class="flex items-center gap-3 min-w-0 group">
                                        @if ($student->avatar_url)
                                            <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" class="size-8 rounded-xl object-cover shrink-0">
                                        @else
                                            <span class="inline-flex items-center justify-center size-8 rounded-xl bg-accent/10 text-accent text-xs font-bold shrink-0">{{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}</span>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium group-hover:text-primary transition-colors truncate">{{ $student->name }}</p>
                                            <p class="text-xs text-base-content/40 truncate">{{ $student->email }}</p>
                                        </div>
                                    </a>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <a href="{{ route('teacher.students.show', [$class, $student]) }}" class="text-xs text-primary hover:underline hidden sm:inline">Performance</a>
                                        <span class="hidden sm:block text-xs text-base-content/40">{{ $student->pivot->enrolled_at ? \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('M d, Y') : '' }}</span>
                                        @if ($class->isActive())
                                            <button type="button" onclick="openUnenrollModal({{ $student->id }}, '{{ e($student->name) }}')" class="size-7 flex items-center justify-center rounded-lg text-base-content/30 hover:text-error hover:bg-error/10 transition-colors" aria-label="Remove">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Sessions --}}
                <div class="d d5 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-base-300/30 flex items-center justify-between">
                        <h2 class="font-semibold text-sm">Sessions</h2>
                        @if ($class->isActive())
                            <button type="button" onclick="document.getElementById('create-session-modal').showModal()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-primary/10 text-primary border border-primary/15 text-xs font-semibold hover:bg-primary/15 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                New Session
                            </button>
                        @endif
                    </div>
                    @if ($class->sessions->isEmpty())
                        <div class="py-10 flex flex-col items-center gap-2 text-center px-6">
                            <p class="text-sm text-base-content/40">No sessions yet.</p>
                        </div>
                    @else
                        <div class="divide-y divide-base-300/30">
                            @foreach ($class->sessions->sortByDesc('start_time') as $session)
                                @php
                                    $sStyle = match ($session->status->value) {
                                        'Active'     => 'text-success bg-success/10 border-success/20',
                                        'Scheduled'  => 'text-info bg-info/10 border-info/20',
                                        'Completed'  => 'text-base-content/50 bg-base-200 border-base-300/50',
                                        'Cancelled'  => 'text-error bg-error/10 border-error/20',
                                        default      => 'text-base-content/50 bg-base-200 border-base-300/50',
                                    };
                                @endphp
                                <a href="{{ route('teacher.sessions.show', $session) }}" class="flex items-center justify-between gap-3 px-5 py-3.5 hover:bg-base-200/40 transition-colors">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium">{{ $session->start_time->format('M d, Y') }}</p>
                                        <p class="text-xs text-base-content/40 mt-0.5">{{ $session->start_time->format('g:i A') }} – {{ $session->end_time->format('g:i A') }} · {{ $session->modality->value }}</p>
                                    </div>
                                    <span class="shrink-0 inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $sStyle }}">{{ $session->status->value }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </main>
    </div>

    {{-- Create Session Modal --}}
    @if ($class->isActive())
    <dialog id="create-session-modal" class="modal">
        <div class="modal-box rounded-2xl">
            <h3 class="text-lg font-semibold mb-4">Schedule New Session</h3>

            @if ($errors->any())
                <div class="alert alert-error text-sm mb-2">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('teacher.sessions.store', $class) }}" class="space-y-4">
                @csrf
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Modality</span></label>
                    <select name="modality" class="select select-bordered rounded-lg w-full" required>
                        <option value="Onsite">Onsite</option>
                        <option value="Online">Online</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Location</span></label>
                    <input type="text" name="location" class="input input-bordered rounded-lg w-full" placeholder="Room or platform URL">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Start Time</span></label>
                        <input type="datetime-local" name="start_time" class="input input-bordered rounded-lg w-full" required>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">End Time</span></label>
                        <input type="datetime-local" name="end_time" class="input input-bordered rounded-lg w-full" required>
                    </div>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Grace Period (minutes)</span></label>
                    <input type="number" name="grace_period_minutes" class="input input-bordered rounded-lg w-full" value="15" min="1" max="60">
                </div>
                <div class="border-t border-base-300/30 pt-4 mt-2 space-y-3">
                    <p class="text-xs font-bold uppercase tracking-widest text-base-content/35">Recurring (optional)</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-medium">Repeat</span></label>
                            <select name="recurrence_pattern" class="select select-bordered rounded-lg w-full">
                                <option value="">No repeat</option>
                                <option value="weekly">Weekly</option>
                                <option value="biweekly">Biweekly</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-medium">Repeat Until</span></label>
                            <input type="date" name="recurrence_end_date" class="input input-bordered rounded-lg w-full">
                        </div>
                    </div>
                </div>
                <div class="modal-action">
                    <button type="button" onclick="this.closest('dialog').close()" class="btn btn-ghost rounded-lg">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-lg">Schedule</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
    @endif

    @if ($class->isActive())
    <script>
        @if ($errors->any())
            document.getElementById('create-session-modal')?.showModal();
        @endif

        const searchInput = document.getElementById('student-search');
        const searchResults = document.getElementById('search-results');
        const selectedContainer = document.getElementById('selected-students');
        const enrollBtn = document.getElementById('enroll-btn');
        const searchUrl = @json(route('teacher.classes.students.search', $class));
        const selectedIds = new Set();
        let debounceTimer = null;

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const q = this.value.trim();
            if (q.length < 2) {
                searchResults.classList.add('hidden');
                searchResults.innerHTML = '';
                return;
            }
            debounceTimer = setTimeout(() => fetchStudents(q), 250);
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                searchResults.classList.add('hidden');
            }
        });

        document.addEventListener('click', function (e) {
            if (!document.getElementById('search-container').contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });

        async function fetchStudents(q) {
            const res = await fetch(searchUrl + '?q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const students = await res.json();
            renderResults(students);
        }

        function renderResults(students) {
            if (students.length === 0) {
                searchResults.innerHTML = '<p class="p-3 text-sm text-base-content/50 text-center">No students found.</p>';
                searchResults.classList.remove('hidden');
                return;
            }

            searchResults.innerHTML = students
                .filter(s => !selectedIds.has(s.id))
                .map(s => `
                    <button type="button" class="flex items-center gap-3 w-full px-3 py-2.5 hover:bg-base-200 transition-colors text-left" onclick="selectStudent(${s.id}, '${escapeHtml(s.name)}', '${escapeHtml(s.email)}', ${s.avatar_url ? `'${escapeHtml(s.avatar_url)}'` : 'null'}, '${escapeHtml(s.initials)}')">
                        ${s.avatar_url
                            ? `<img src="${escapeHtml(s.avatar_url)}" alt="${escapeHtml(s.name)}" class="size-8 rounded-lg object-cover shrink-0">`
                            : `<span class="inline-flex items-center justify-center size-8 rounded-lg bg-primary/15 text-primary text-xs font-bold shrink-0">${escapeHtml(s.initials)}</span>`
                        }
                        <div class="min-w-0">
                            <p class="text-sm font-medium truncate">${escapeHtml(s.name)}</p>
                            <p class="text-xs text-base-content/60 truncate">${escapeHtml(s.email)}</p>
                        </div>
                    </button>
                `).join('');

            if (searchResults.innerHTML === '') {
                searchResults.innerHTML = '<p class="p-3 text-sm text-base-content/50 text-center">No more students match.</p>';
            }

            searchResults.classList.remove('hidden');
        }

        function selectStudent(id, name, email, avatarUrl, initials) {
            if (selectedIds.has(id)) return;
            selectedIds.add(id);

            const row = document.createElement('div');
            row.className = 'flex items-center gap-3 rounded-xl border border-base-300 bg-base-200/50 px-3 py-2';
            row.dataset.studentId = id;
            row.innerHTML = `
                <input type="hidden" name="students[]" value="${id}">
                ${avatarUrl
                    ? `<img src="${escapeHtml(avatarUrl)}" alt="${escapeHtml(name)}" class="size-8 rounded-lg object-cover shrink-0">`
                    : `<span class="inline-flex items-center justify-center size-8 rounded-lg bg-primary/15 text-primary text-xs font-bold shrink-0">${escapeHtml(initials)}</span>`
                }
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium truncate">${escapeHtml(name)}</p>
                    <p class="text-xs text-base-content/60 truncate">${escapeHtml(email)}</p>
                </div>
                <button type="button" onclick="deselectStudent(${id}, this)" class="btn btn-ghost btn-xs btn-square rounded-lg text-base-content/40 hover:text-error hover:bg-error/10 shrink-0" aria-label="Remove">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
            `;

            selectedContainer.appendChild(row);
            updateEnrollBtn();
            searchResults.classList.add('hidden');
            searchInput.value = '';
            searchInput.focus();
        }

        function deselectStudent(id, btn) {
            selectedIds.delete(id);
            btn.closest('[data-student-id]').remove();
            updateEnrollBtn();
        }

        function updateEnrollBtn() {
            const count = selectedIds.size;
            enrollBtn.classList.toggle('hidden', count === 0);
            enrollBtn.textContent = count === 1 ? 'Enroll 1 Student' : `Enroll ${count} Students`;
        }

        function escapeHtml(str) {
            if (!str) return '';
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }
    </script>
    @endif
    {{-- Archive Class Modal --}}
    @if ($class->isActive())
    <dialog id="archive-class-modal" class="modal">
        <div class="modal-box rounded-2xl max-w-sm">
            <h3 class="text-lg font-bold">Archive Class</h3>
            <p class="text-sm text-base-content/60 mt-2">Are you sure you want to archive <strong>{{ $class->name }}</strong>? Students will no longer be able to attend sessions.</p>
            <div class="modal-action">
                <button type="button" onclick="this.closest('dialog').close()" class="btn btn-ghost rounded-xl">Cancel</button>
                <form method="POST" action="{{ route('teacher.classes.archive', $class) }}">
                    @csrf
                    <button type="submit" class="btn btn-error rounded-xl">Archive</button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- Unenroll Student Modal --}}
    <dialog id="unenroll-student-modal" class="modal">
        <div class="modal-box rounded-2xl max-w-sm">
            <h3 class="text-lg font-bold">Remove Student</h3>
            <p class="text-sm text-base-content/60 mt-2">Are you sure you want to remove <strong id="unenroll-student-name"></strong> from this class?</p>
            <div class="modal-action">
                <button type="button" onclick="this.closest('dialog').close()" class="btn btn-ghost rounded-xl">Cancel</button>
                <form method="POST" id="unenroll-form" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-error rounded-xl">Remove</button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    <script>
        function openUnenrollModal(studentId, studentName) {
            document.getElementById('unenroll-student-name').textContent = studentName;
            document.getElementById('unenroll-form').action = @json(route('teacher.classes.unenroll', [$class, '__ID__'])).replace('__ID__', studentId);
            document.getElementById('unenroll-student-modal').showModal();
        }
    </script>
    @endif
</x-layouts.app>
