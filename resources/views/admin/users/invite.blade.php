<x-layouts.app title="Invite Users">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="users" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8">
                <div class="max-w-2xl">

                    <div class="d d1 mb-6">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/40 hover:text-base-content transition-colors mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back to Users
                        </a>
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Admin</p>
                                <h1 id="page-title" class="text-2xl md:text-3xl font-black tracking-tight">Invite User</h1>
                                <p class="mt-1 text-sm text-base-content/50">Send an invitation email to a new teacher or student.</p>
                            </div>
                            <span id="count-badge" class="hidden inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-primary bg-primary/10 border-primary/20 mt-2 shrink-0">1 invitee</span>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="d d2 rounded-2xl border border-error/30 bg-error/5 px-4 py-3 mb-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <p class="text-sm text-error">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form id="invite-form" method="POST" action="{{ route('admin.users.invite.send') }}" class="d d2">
                        @csrf

                        <div id="invitees-list" class="space-y-3">
                            <div class="invite-row af-card overflow-hidden !p-0" data-index="0">
                                <div class="px-5 py-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <div>
                                                <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="name-0">Full Name <span class="text-base-content/25 font-normal normal-case tracking-normal">(optional)</span></label>
                                                <input
                                                    type="text"
                                                    id="name-0"
                                                    name="invitees[0][name]"
                                                    value="{{ old('invitees.0.name') }}"
                                                    class="af-input {{ $errors->has('invitees.0.name') ? 'af-input-error' : '' }}"
                                                    placeholder="Invitee's full name"
                                                    autofocus
                                                >
                                            </div>
                                            <div>
                                                <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="email-0">Email Address</label>
                                                <input
                                                    type="email"
                                                    id="email-0"
                                                    name="invitees[0][email]"
                                                    value="{{ old('invitees.0.email') }}"
                                                    class="af-input {{ $errors->has('invitees.0.email') ? 'af-input-error' : '' }}"
                                                    placeholder="invitee@example.com"
                                                    required
                                                >
                                            </div>
                                            <div>
                                                <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="role-0">Role</label>
                                                <select
                                                    id="role-0"
                                                    name="invitees[0][role]"
                                                    class="af-input {{ $errors->has('invitees.0.role') ? 'af-input-error' : '' }}"
                                                    required
                                                >
                                                    <option value="" disabled {{ old('invitees.0.role') ? '' : 'selected' }}>Select role</option>
                                                    <option value="Admin" {{ old('invitees.0.role') === 'Admin' ? 'selected' : '' }}>Admin</option>
                                                    <option value="Teacher" {{ old('invitees.0.role') === 'Teacher' ? 'selected' : '' }}>Teacher</option>
                                                    <option value="Student" {{ old('invitees.0.role') === 'Student' ? 'selected' : '' }}>Student</option>
                                                </select>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            class="remove-row-btn inline-flex items-center justify-center size-8 rounded-xl text-base-content/30 hover:text-error hover:bg-error/10 mt-7 hidden shrink-0 transition-colors"
                                            aria-label="Remove this invitee"
                                            onclick="removeRow(this)"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                                <path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            id="add-row-btn"
                            onclick="addRow()"
                            class="mt-3 w-full flex items-center justify-center gap-2 rounded-2xl border border-dashed border-base-300/70 bg-transparent hover:border-primary hover:text-primary hover:bg-primary/5 py-3 text-sm text-base-content/40 transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Add another invitee
                        </button>

                        <div class="flex items-center gap-3 mt-5">
                            <x-ui.button type="submit" variant="primary" id="submit-btn">Send Invitation</x-ui.button>
                            <x-ui.button href="{{ route('admin.users.index') }}" variant="ghost">Cancel</x-ui.button>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>

    <script>
        const list = document.getElementById('invitees-list');
        const submitBtn = document.getElementById('submit-btn');
        const pageTitle = document.getElementById('page-title');
        const countBadge = document.getElementById('count-badge');

        function rowCount() {
            return list.querySelectorAll('.invite-row').length;
        }

        function updateUI() {
            const count = rowCount();
            const isBulk = count > 1;

            // Page title
            pageTitle.textContent = isBulk ? 'Invite Users' : 'Invite User';

            // Count badge
            countBadge.textContent = count + (count === 1 ? ' invitee' : ' invitees');
            countBadge.classList.toggle('hidden', !isBulk);

            // Submit button label
            submitBtn.textContent = isBulk ? `Send ${count} Invitations` : 'Send Invitation';

            // Show/hide remove buttons (only when 2+)
            list.querySelectorAll('.remove-row-btn').forEach(btn => {
                btn.classList.toggle('hidden', !isBulk);
            });

            // Re-index all rows
            list.querySelectorAll('.invite-row').forEach((row, i) => {
                row.dataset.index = i;
                row.querySelector('input[type="text"]').name = `invitees[${i}][name]`;
                row.querySelector('input[type="text"]').id = `name-${i}`;
                row.querySelector('input[type="email"]').name = `invitees[${i}][email]`;
                row.querySelector('input[type="email"]').id = `email-${i}`;
                row.querySelector('select').name = `invitees[${i}][role]`;
                row.querySelector('select').id = `role-${i}`;
            });
        }

        function addRow() {
            const template = list.querySelector('.invite-row');
            const clone = template.cloneNode(true);

            // Clear cloned values
            clone.querySelector('input[type="text"]').value = '';
            clone.querySelector('input[type="email"]').value = '';
            const select = clone.querySelector('select');
            select.selectedIndex = 0;

            list.appendChild(clone);
            updateUI();

            // Focus the new email input
            clone.querySelector('input[type="email"]').focus();
        }

        function removeRow(btn) {
            if (rowCount() <= 1) return;
            btn.closest('.invite-row').remove();
            updateUI();
        }

        // Init
        updateUI();
    </script>
</x-layouts.app>

