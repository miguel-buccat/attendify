<x-layouts.app :title="$class->name">
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
                                <x-ui.badge variant="success">Active</x-ui.badge>
                            @else
                                <x-ui.badge variant="neutral">Archived</x-ui.badge>
                            @endif
                            @if ($class->isActive())
                                <x-ui.button variant="ghost" size="xs" onclick="document.getElementById('archive-class-modal').showModal()">Archive</x-ui.button>
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
                <div class="d d2 af-card overflow-hidden !p-0">
                    <details class="group">
                        <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer hover:bg-base-content/[.03] transition-colors list-none">
                            <span class="font-semibold text-sm">Class Details</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 text-base-content/40 transition-transform group-open:rotate-180"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </summary>
                        <div class="px-5 pb-5 pt-2 space-y-4 border-t af-divider">
                            @if ($class->description)
                                <p class="text-sm text-base-content/60">{{ $class->description }}</p>
                            @endif
                            @if ($class->isActive())
                            <form method="POST" action="{{ route('teacher.classes.update', $class) }}" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <x-form.field name="name" label="Name">
                                        <input id="edit-name" type="text" name="name" value="{{ old('name', $class->name) }}" class="af-input @error('name') af-input-error @enderror" required>
                                    </x-form.field>
                                    <x-form.field name="section" label="Section">
                                        <input id="edit-section" type="text" name="section" value="{{ old('section', $class->section) }}" class="af-input">
                                    </x-form.field>
                                </div>
                                <x-form.field name="description" label="Description">
                                    <textarea id="edit-desc" name="description" rows="2" class="af-input">{{ old('description', $class->description) }}</textarea>
                                </x-form.field>
                                <x-ui.button type="submit" variant="primary" size="sm">Save Changes</x-ui.button>
                            </form>
                            @endif
                        </div>
                    </details>
                </div>
                @endif

                {{-- Enroll Students --}}
                @if ($class->isActive())
                <div class="d d3 af-card overflow-hidden !p-0">
                    <div class="px-5 py-4 border-b af-divider">
                        <h2 class="font-semibold text-sm">Enroll Students</h2>
                        <p class="text-xs text-base-content/40 mt-0.5">Search by name or email to add students to this class.</p>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="relative" id="search-container">
                            <input type="text" id="student-search"
                                class="af-input"
                                placeholder="Type a student name or email…"
                                autocomplete="off" name="student_search_nonce" role="presentation">
                            <div id="search-results" class="absolute z-20 top-full left-0 right-0 mt-1 af-card !p-0 shadow-xl hidden max-h-64 overflow-y-auto"></div>
                        </div>
                        <form method="POST" action="{{ route('teacher.classes.enroll', $class) }}" id="enroll-form">
                            @csrf
                            <div id="selected-students" class="space-y-2"></div>
                            <button type="submit" id="enroll-btn" class="af-btn af-btn-primary btn-sm rounded-xl mt-3 hidden">Enroll Students</button>
                        </form>
                    </div>
                </div>
                @endif

                {{-- Student Roster --}}
                <div class="d d4 af-card overflow-hidden !p-0">
                    <div class="px-5 py-4 border-b af-divider flex items-center justify-between">
                        <x-ui.section-header label="Students" :count="$class->students->count()" action-href="{{ route('teacher.classes.analytics.pdf', $class) }}" action-label="Export PDF" />
                    </div>
                    @if ($class->students->isEmpty())
                        <x-ui.empty-state
                            icon="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"
                            title="No students enrolled"
                            description="No students enrolled yet."
                        />
                    @else
                        <div class="divide-y af-divider">
                            @foreach ($class->students as $student)
                                <div class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-base-content/[.03] transition-colors">
                                    <a href="{{ route('profile.show', $student) }}" class="flex items-center gap-3 min-w-0 group">
                                        @if ($student->avatar_url)
                                            <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" class="size-8 rounded-xl object-cover ring-1 ring-base-300/30 shrink-0">
                                        @else
                                            <span class="inline-flex items-center justify-center size-8 rounded-xl bg-accent/10 text-accent ring-1 ring-accent/15 text-xs font-bold shrink-0">{{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}</span>
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
                <div class="d d5 af-card overflow-hidden !p-0">
                    <div class="px-5 py-4 border-b af-divider flex items-center justify-between">
                        <h2 class="font-semibold text-sm">Sessions</h2>
                        @if ($class->isActive())
                            <div class="flex items-center gap-2">
                                <x-ui.button variant="ghost" size="xs" onclick="document.getElementById('preschedule-modal').showModal()">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Pre-Schedule
                                </x-ui.button>
                                <x-ui.button variant="outline" size="xs" onclick="document.getElementById('create-session-modal').showModal()">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                    New Session
                                </x-ui.button>
                            </div>
                        @endif
                    </div>
                    @if ($class->sessions->isEmpty())
                        <x-ui.empty-state
                            icon="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                            title="No sessions yet"
                            description="Schedule your first session to get started."
                        />
                    @else
                        <div class="divide-y af-divider">
                            @foreach ($class->sessions->sortBy('start_time') as $session)
                                @php
                                    $sVariant = match ($session->status->value) {
                                        'Active'    => 'success',
                                        'Scheduled' => 'info',
                                        'Cancelled' => 'error',
                                        default     => 'neutral',
                                    };
                                @endphp
                                <a href="{{ route('teacher.sessions.show', $session) }}" class="flex items-center justify-between gap-3 px-5 py-3.5 hover:bg-base-content/[.03] transition-colors">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold">{{ $session->start_time->format('M d, Y') }}</p>
                                        <p class="text-xs text-base-content/40 mt-0.5">{{ $session->start_time->format('g:i A') }} – {{ $session->end_time->format('g:i A') }} · {{ $session->modality->value }}</p>
                                    </div>
                                    <x-ui.badge :variant="$sVariant" size="xs">{{ $session->status->value }}</x-ui.badge>
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
        <div class="af-modal-box modal-box rounded-2xl border border-base-300/30 shadow-2xl">
            <h3 class="text-lg font-semibold mb-4">Schedule New Session</h3>

            @if ($errors->any())
                <x-ui.alert variant="error" class="mb-2">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('teacher.sessions.store', $class) }}" class="space-y-4">
                @csrf
                <x-form.field name="modality" label="Modality">
                    <select name="modality" class="af-input" required>
                        <option value="Onsite">Onsite</option>
                        <option value="Online">Online</option>
                    </select>
                </x-form.field>
                <x-form.field name="location" label="Location">
                    <input type="text" name="location" class="af-input" placeholder="Room or platform URL">
                </x-form.field>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <x-form.field name="start_time" label="Start Time">
                        <input type="datetime-local" name="start_time" class="af-input" required>
                    </x-form.field>
                    <x-form.field name="end_time" label="End Time">
                        <input type="datetime-local" name="end_time" class="af-input" required>
                    </x-form.field>
                </div>
                <x-form.field name="grace_period_minutes" label="Grace Period (minutes)">
                    <input type="number" name="grace_period_minutes" class="af-input" value="15" min="1" max="60">
                </x-form.field>
                <div class="modal-action">
                    <x-ui.button type="button" variant="ghost" onclick="this.closest('dialog').close()">Cancel</x-ui.button>
                    <x-ui.button type="submit" variant="primary">Schedule</x-ui.button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- Pre-Schedule Modal --}}
    <dialog id="preschedule-modal" class="modal">
        <div class="af-modal-box modal-box rounded-2xl border border-base-300/30 shadow-2xl max-w-lg">
            <h3 class="text-lg font-semibold mb-1">Pre-Schedule Sessions</h3>
            <p class="text-xs text-base-content/40 mb-4">Create recurring sessions for specific days of the week.</p>

            @if ($errors->hasBag('preschedule'))
                <x-ui.alert variant="error" class="mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->getBag('preschedule')->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('teacher.sessions.bulk-store', $class) }}" class="space-y-5">
                @csrf

                {{-- Day checkboxes --}}
                <div>
                    <label class="label pb-2"><span class="label-text font-medium">Days</span></label>
                    <div class="flex flex-wrap gap-2">
                        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="days[]" value="{{ $day }}" class="peer hidden">
                                <span class="inline-flex items-center px-3.5 py-2 rounded-xl border border-base-300/50 bg-base-200/50 text-sm font-medium text-base-content/50 transition-all select-none peer-checked:bg-primary peer-checked:text-primary-content peer-checked:border-primary peer-checked:shadow-sm hover:bg-base-300/50 peer-checked:hover:bg-primary/90">
                                    {{ $day }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <x-form.field name="modality" label="Modality">
                        <select name="modality" class="af-input" required>
                            <option value="Onsite">Onsite</option>
                            <option value="Online">Online</option>
                        </select>
                    </x-form.field>
                    <x-form.field name="location" label="Location">
                        <input type="text" name="location" class="af-input" placeholder="Room or URL">
                    </x-form.field>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <x-form.field name="start_time" label="Start Time">
                        <input type="time" name="start_time" class="af-input" required>
                    </x-form.field>
                    <x-form.field name="end_time" label="End Time">
                        <input type="time" name="end_time" class="af-input" required>
                    </x-form.field>
                </div>

                <x-form.field name="grace_period_minutes" label="Grace Period (minutes)">
                    <input type="number" name="grace_period_minutes" class="af-input" value="15" min="1" max="60">
                </x-form.field>

                <div class="border-t af-divider pt-4 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-form.field name="interval_weeks" label="Repeat Every">
                            <select name="interval_weeks" class="af-input" required>
                                <option value="1">Every week</option>
                                <option value="2">Every 2 weeks</option>
                                <option value="3">Every 3 weeks</option>
                                <option value="4">Every 4 weeks</option>
                            </select>
                        </x-form.field>
                        <x-form.field name="start_date" label="Starting From">
                            <input type="date" name="start_date" class="af-input" required>
                        </x-form.field>
                    </div>
                    <x-form.field name="end_date" label="Until">
                        <input type="date" name="end_date" class="af-input" required>
                    </x-form.field>
                </div>

                <div class="modal-action">
                    <x-ui.button type="button" variant="ghost" onclick="this.closest('dialog').close()">Cancel</x-ui.button>
                    <x-ui.button type="submit" variant="primary">Schedule Sessions</x-ui.button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
    @endif

    @if ($class->isActive())
    <script>
        @if ($errors->any() && !$errors->hasBag('preschedule'))
            document.getElementById('create-session-modal')?.showModal();
        @endif
        @if ($errors->hasBag('preschedule'))
            document.getElementById('preschedule-modal')?.showModal();
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
        <div class="af-modal-box modal-box rounded-2xl border border-base-300/30 shadow-2xl max-w-sm">
            <h3 class="text-lg font-bold">Archive Class</h3>
            <p class="text-sm text-base-content/60 mt-2">Are you sure you want to archive <strong>{{ $class->name }}</strong>? Students will no longer be able to attend sessions.</p>
            <div class="modal-action">
                <x-ui.button type="button" variant="ghost" onclick="this.closest('dialog').close()">Cancel</x-ui.button>
                <form method="POST" action="{{ route('teacher.classes.archive', $class) }}">
                    @csrf
                    <x-ui.button type="submit" variant="danger">Archive</x-ui.button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- Unenroll Student Modal --}}
    <dialog id="unenroll-student-modal" class="modal">
        <div class="af-modal-box modal-box rounded-2xl border border-base-300/30 shadow-2xl max-w-sm">
            <h3 class="text-lg font-bold">Remove Student</h3>
            <p class="text-sm text-base-content/60 mt-2">Are you sure you want to remove <strong id="unenroll-student-name"></strong> from this class?</p>
            <div class="modal-action">
                <x-ui.button type="button" variant="ghost" onclick="this.closest('dialog').close()">Cancel</x-ui.button>
                <form method="POST" id="unenroll-form" action="">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="danger">Remove</x-ui.button>
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
