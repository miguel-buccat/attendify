<x-layouts.app title="Invite Users">
    <div class="flex min-h-screen bg-base-200">
        {{-- Sidebar --}}
        <aside class="hidden lg:flex flex-col w-64 shrink-0 bg-base-100 border-r border-base-300 min-h-screen">
            <div class="flex items-center gap-3 p-5 border-b border-base-300">
                <div class="size-9 rounded-lg border border-base-300 bg-base-200 p-1.5 shrink-0">
                    <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" class="h-full w-full object-contain">
                </div>
                <span class="font-semibold truncate text-sm">{{ $institutionName }}</span>
            </div>

            <nav class="flex-1 p-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-base-200 text-base-content/80 text-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                        <path d="M4 11.5 12 5l8 6.5V20a1 1 0 0 1-1 1h-4v-6H9v6H5a1 1 0 0 1-1-1v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-primary/10 text-primary font-medium text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 4v6m3-3h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Manage Users
                </a>
            </nav>

            <div class="p-3 border-t border-base-300">
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-base-200 transition-colors">
                    <span class="inline-flex items-center justify-center size-8 rounded-lg bg-primary/15 text-primary text-xs font-bold shrink-0">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-base-content/60">Admin</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('logout-modal').showModal()" class="btn btn-ghost btn-sm w-full justify-start gap-2 rounded-xl text-base-content/70 normal-case font-normal mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Logout
                </button>
            </div>
        </aside>

        {{-- Mobile header --}}
        <div class="lg:hidden fixed top-0 inset-x-0 z-30 flex items-center justify-between px-4 h-14 bg-base-100 border-b border-base-300">
            <div class="flex items-center gap-2">
                <div class="size-7 rounded-md border border-base-300 bg-base-200 p-1 shrink-0">
                    <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" class="h-full w-full object-contain">
                </div>
                <span class="font-semibold text-sm truncate max-w-36">{{ $institutionName }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge badge-primary badge-sm">Admin</span>
                <button type="button" onclick="document.getElementById('logout-modal').showModal()" class="btn btn-ghost btn-xs rounded-lg">Logout</button>
            </div>
        </div>

        {{-- Main content --}}
        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8">
                <div class="max-w-2xl">

                    {{-- Page header --}}
                    <div class="mb-6">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/60 hover:text-base-content transition-colors mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back to Users
                        </a>
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h1 id="page-title" class="text-2xl md:text-3xl font-semibold">Invite User</h1>
                                <p class="mt-1 text-base-content/60">Send an invitation email to a new teacher or student.</p>
                            </div>
                            <span id="count-badge" class="badge badge-primary badge-lg hidden mt-1 shrink-0">1 invitee</span>
                        </div>
                    </div>

                    {{-- Validation errors --}}
                    @if ($errors->any())
                        <div class="rounded-xl border border-error/30 bg-error/5 p-4 mb-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <p class="text-sm text-error">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form id="invite-form" method="POST" action="{{ route('admin.users.invite.send') }}">
                        @csrf

                        {{-- Invitee rows --}}
                        <div id="invitees-list" class="space-y-3">
                            <div class="invite-row rounded-xl border border-base-300 bg-base-100 p-4" data-index="0">
                                <div class="flex items-start gap-3">
                                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div class="form-control">
                                            <label class="label pb-1" for="email-0">
                                                <span class="label-text font-medium text-sm">Email Address</span>
                                            </label>
                                            <input
                                                type="email"
                                                id="email-0"
                                                name="invitees[0][email]"
                                                value="{{ old('invitees.0.email') }}"
                                                class="input input-bordered w-full rounded-xl input-sm h-10 @error('invitees.0.email') input-error @enderror"
                                                placeholder="invitee@example.com"
                                                required
                                                autofocus
                                            >
                                        </div>
                                        <div class="form-control">
                                            <label class="label pb-1" for="role-0">
                                                <span class="label-text font-medium text-sm">Role</span>
                                            </label>
                                            <select
                                                id="role-0"
                                                name="invitees[0][role]"
                                                class="select select-bordered w-full rounded-xl select-sm h-10 @error('invitees.0.role') select-error @enderror"
                                                required
                                            >
                                                <option value="" disabled {{ old('invitees.0.role') ? '' : 'selected' }}>Select role</option>
                                                <option value="Teacher" {{ old('invitees.0.role') === 'Teacher' ? 'selected' : '' }}>Teacher</option>
                                                <option value="Student" {{ old('invitees.0.role') === 'Student' ? 'selected' : '' }}>Student</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        class="remove-row-btn btn btn-ghost btn-sm btn-square rounded-lg text-base-content/40 hover:text-error hover:bg-error/10 mt-7 hidden shrink-0"
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

                        {{-- Add another row button --}}
                        <button
                            type="button"
                            id="add-row-btn"
                            onclick="addRow()"
                            class="mt-3 w-full flex items-center justify-center gap-2 rounded-xl border border-dashed border-base-300 bg-transparent hover:border-primary hover:text-primary hover:bg-primary/5 py-2.5 text-sm text-base-content/50 transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Add another invitee
                        </button>

                        {{-- Actions --}}
                        <div class="flex items-center gap-3 mt-5">
                            <button type="submit" id="submit-btn" class="btn btn-primary rounded-xl normal-case">
                                Send Invitation
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost rounded-xl normal-case">Cancel</a>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>

    {{-- Logout confirmation modal --}}
    <dialog id="logout-modal" class="modal">
        <div class="modal-box rounded-2xl">
            <h3 class="text-lg font-semibold">Confirm Logout</h3>
            <p class="mt-2 text-base-content/70">Are you sure you want to log out?</p>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-ghost rounded-xl">Cancel</button>
                </form>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-error rounded-xl">Logout</button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

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

