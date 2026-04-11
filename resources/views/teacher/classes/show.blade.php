<x-layouts.app :title="$class->name">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                {{-- Back link + header --}}
                <div>
                    <a href="{{ route('teacher.classes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/60 hover:text-base-content transition-colors mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                            <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back to Classes
                    </a>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-semibold">{{ $class->name }}</h1>
                            @if ($class->section)
                                <p class="mt-1 text-base-content/60">{{ $class->section }}</p>
                            @endif
                        </div>
                        @if ($class->status->value === 'Active')
                            <span class="badge badge-success badge-lg mt-1 shrink-0">Active</span>
                        @else
                            <span class="badge badge-ghost badge-lg mt-1 shrink-0">Archived</span>
                        @endif
                    </div>
                </div>

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                {{-- Class info + actions --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Details card --}}
                    <div class="lg:col-span-2 rounded-xl border border-base-300 bg-base-100 p-6 space-y-4">
                        @if ($class->description)
                            <p class="text-base-content/70">{{ $class->description }}</p>
                        @endif

                        {{-- Edit form --}}
                        <details class="group">
                            <summary class="cursor-pointer text-sm font-medium text-base-content/70 hover:text-base-content list-none flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 group-open:hidden" aria-hidden="true">
                                    <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 hidden group-open:block" aria-hidden="true">
                                    <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Edit Class Details
                            </summary>
                            <form method="POST" action="{{ route('teacher.classes.update', $class) }}" class="mt-3 space-y-3">
                                @csrf
                                @method('PATCH')

                                <div class="form-control">
                                    <label class="label pb-1" for="edit-name"><span class="label-text text-sm">Name</span></label>
                                    <input id="edit-name" type="text" name="name" value="{{ old('name', $class->name) }}" class="input input-bordered w-full rounded-xl input-sm h-10 @error('name') input-error @enderror" required>
                                    @error('name') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                                </div>
                                <div class="form-control">
                                    <label class="label pb-1" for="edit-section"><span class="label-text text-sm">Section</span></label>
                                    <input id="edit-section" type="text" name="section" value="{{ old('section', $class->section) }}" class="input input-bordered w-full rounded-xl input-sm h-10">
                                </div>
                                <div class="form-control">
                                    <label class="label pb-1" for="edit-desc"><span class="label-text text-sm">Description</span></label>
                                    <textarea id="edit-desc" name="description" rows="2" class="textarea textarea-bordered w-full rounded-xl">{{ old('description', $class->description) }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm rounded-xl">Save Changes</button>
                            </form>
                        </details>
                    </div>

                    {{-- Actions sidebar --}}
                    <div class="space-y-3">
                        @if ($class->isActive())
                            <form method="POST" action="{{ route('teacher.classes.archive', $class) }}" onsubmit="return confirm('Archive this class? It will no longer accept new sessions.')">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-sm rounded-xl w-full text-base-content/60">Archive Class</button>
                            </form>
                        @endif
                    </div>

                </div>

                {{-- Enroll Students --}}
                @if ($class->isActive())
                    <div class="rounded-xl border border-base-300 bg-base-100">
                        <div class="p-4 border-b border-base-300">
                            <h2 class="font-semibold">Enroll Students</h2>
                            <p class="text-sm text-base-content/60 mt-0.5">Search by name or email to add students to this class.</p>
                        </div>
                        <div class="p-4 space-y-3">
                            {{-- Search input --}}
                            <div class="relative" id="search-container">
                                <input
                                    type="text"
                                    id="student-search"
                                    class="input input-bordered w-full rounded-xl input-sm h-10"
                                    placeholder="Type a student name or email..."
                                    autocomplete="off"
                                >
                                <div id="search-results" class="absolute z-20 top-full left-0 right-0 mt-1 rounded-xl border border-base-300 bg-base-100 shadow-lg hidden max-h-64 overflow-y-auto"></div>
                            </div>

                            {{-- Selected students (to enroll) --}}
                            <form method="POST" action="{{ route('teacher.classes.enroll', $class) }}" id="enroll-form">
                                @csrf
                                <div id="selected-students" class="space-y-2"></div>
                                <button type="submit" id="enroll-btn" class="btn btn-primary btn-sm rounded-xl mt-3 hidden">
                                    Enroll Students
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Student Roster --}}
                <div class="rounded-xl border border-base-300 bg-base-100">
                    <div class="flex items-center justify-between p-4 border-b border-base-300">
                        <h2 class="font-semibold">Students <span class="text-base-content/50 font-normal">({{ $class->students->count() }})</span></h2>
                    </div>
                    @if ($class->students->isEmpty())
                        <p class="p-6 text-center text-sm text-base-content/50">No students enrolled yet. Use the search above to add students.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th class="hidden sm:table-cell">Email</th>
                                        <th class="hidden md:table-cell">Enrolled</th>
                                        @if ($class->isActive())
                                            <th class="w-12"></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($class->students as $student)
                                        <tr>
                                            <td>
                                                <a href="{{ route('profile.show', $student) }}" class="flex items-center gap-2.5 group">
                                                    @if ($student->avatar_url)
                                                        <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" class="size-7 rounded-lg object-cover shrink-0">
                                                    @else
                                                        <span class="inline-flex items-center justify-center size-7 rounded-lg bg-primary/15 text-primary text-xs font-bold shrink-0">
                                                            {{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}
                                                        </span>
                                                    @endif
                                                    <div class="min-w-0">
                                                        <span class="font-medium group-hover:text-primary transition-colors block truncate">{{ $student->name }}</span>
                                                        <span class="text-xs text-base-content/50 sm:hidden block truncate">{{ $student->email }}</span>
                                                    </div>
                                                </a>
                                            </td>
                                            <td class="text-base-content/60 hidden sm:table-cell">{{ $student->email }}</td>
                                            <td class="text-base-content/60 hidden md:table-cell">{{ $student->pivot->enrolled_at ? \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('M d, Y') : '-' }}</td>
                                            @if ($class->isActive())
                                                <td>
                                                    <form method="POST" action="{{ route('teacher.classes.unenroll', [$class, $student]) }}" onsubmit="return confirm('Remove {{ $student->name }} from this class?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-ghost btn-xs btn-square rounded-lg text-base-content/40 hover:text-error hover:bg-error/10" aria-label="Remove student">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5" aria-hidden="true">
                                                                <path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Sessions section --}}
                <div class="card bg-base-100 rounded-xl border border-base-300">
                    <div class="card-body gap-4">
                        <div class="flex items-center justify-between">
                            <h2 class="card-title text-lg">Sessions</h2>
                            @if ($class->isActive())
                                <button type="button" onclick="document.getElementById('create-session-modal').showModal()" class="btn btn-primary btn-sm rounded-lg">
                                    New Session
                                </button>
                            @endif
                        </div>

                        @if ($class->sessions->isEmpty())
                            <p class="text-base-content/50 text-sm">No sessions yet.</p>
                        @else
                            {{-- Mobile: card layout --}}
                            <div class="space-y-3 sm:hidden">
                                @foreach ($class->sessions->sortByDesc('start_time') as $session)
                                    <a href="{{ route('teacher.sessions.show', $session) }}" class="block rounded-xl border border-base-300 bg-base-200/40 p-4 space-y-2 active:bg-base-200 transition-colors">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-medium">{{ $session->start_time->format('M d, Y') }}</span>
                                            @php
                                                $sBadge = match ($session->status->value) {
                                                    'Active' => 'badge-success',
                                                    'Scheduled' => 'badge-info',
                                                    'Completed' => 'badge-ghost',
                                                    'Cancelled' => 'badge-error',
                                                    default => 'badge-ghost',
                                                };
                                            @endphp
                                            <span class="badge {{ $sBadge }} badge-sm">{{ $session->status->value }}</span>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm text-base-content/60">
                                            <span>{{ $session->start_time->format('g:i A') }} – {{ $session->end_time->format('g:i A') }}</span>
                                            <span class="text-base-content/30">·</span>
                                            <span>{{ $session->modality->value }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            {{-- Desktop: table layout --}}
                            <div class="hidden sm:block overflow-x-auto">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Modality</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($class->sessions->sortByDesc('start_time') as $session)
                                            <tr>
                                                <td class="font-medium">{{ $session->start_time->format('M d, Y') }}</td>
                                                <td class="text-base-content/60">{{ $session->start_time->format('g:i A') }} – {{ $session->end_time->format('g:i A') }}</td>
                                                <td>{{ $session->modality->value }}</td>
                                                <td>
                                                    @php
                                                        $sBadge = match ($session->status->value) {
                                                            'Active' => 'badge-success',
                                                            'Scheduled' => 'badge-info',
                                                            'Completed' => 'badge-ghost',
                                                            'Cancelled' => 'badge-error',
                                                            default => 'badge-ghost',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $sBadge }} badge-sm">{{ $session->status->value }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('teacher.sessions.show', $session) }}" class="btn btn-ghost btn-xs rounded-lg">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
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
</x-layouts.app>
