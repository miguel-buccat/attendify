<x-layouts.app title="Manage Users">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="users" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                <div class="d d1 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Admin</p>
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight">Manage Users</h1>
                        <p class="mt-1 text-sm text-base-content/50">All registered users and pending invitations.</p>
                    </div>
                    <button
                        type="button"
                        onclick="document.getElementById('invite-modal').showModal()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-primary-content text-sm font-semibold hover:opacity-90 transition-opacity self-start"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Invite User
                    </button>
                </div>

                {{-- Search & Filters --}}
                <form method="GET" action="{{ route('admin.users.index') }}" class="d d2 flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 absolute left-3 top-1/2 -translate-y-1/2 text-base-content/30" aria-hidden="true">
                            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.8"/>
                            <path d="m21 21-4.3-4.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="af-input pl-10!">
                    </div>
                    <select name="role" onchange="this.form.submit()" class="af-select w-full sm:w-40 shrink-0">
                        <option value="">All Roles</option>
                        <option value="Admin" {{ request('role') === 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Teacher" {{ request('role') === 'Teacher' ? 'selected' : '' }}>Teacher</option>
                        <option value="Student" {{ request('role') === 'Student' ? 'selected' : '' }}>Student</option>
                    </select>
                    <x-ui.button type="submit" variant="outline">Search</x-ui.button>
                    @if (request('search') || request('role'))
                        <x-ui.button href="{{ route('admin.users.index') }}" variant="ghost">Clear</x-ui.button>
                    @endif
                </form>

                {{-- Users list --}}
                <div class="d d3 af-card overflow-hidden !p-0">
                    <div class="px-5 py-4 border-b af-divider flex items-center justify-between">
                        <h2 class="font-semibold text-sm">Registered Users</h2>
                        <span class="text-xs text-base-content/40">{{ $users->total() }}</span>
                    </div>
                    <div class="divide-y af-divider">
                        @forelse ($users as $registeredUser)
                            @php
                                $roleVariant = match ($registeredUser->role->value) {
                                    'Admin'   => 'primary',
                                    'Teacher' => 'secondary',
                                    default   => 'accent',
                                };
                                $statusVariant = match ($registeredUser->status->value) {
                                    'blocked'  => 'warning',
                                    'archived' => 'error',
                                    default    => null,
                                };
                            @endphp
                            <button type="button" onclick="openUserModal({{ $registeredUser->id }})" class="w-full flex items-center justify-between gap-3 px-5 py-3 hover:bg-base-content/[.03] transition-colors group text-left">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    @if ($registeredUser->avatarUrl)
                                        <img src="{{ $registeredUser->avatarUrl }}" class="size-8 rounded-full object-cover shrink-0" alt="">
                                    @else
                                        <div class="size-8 rounded-full bg-base-200 flex items-center justify-center text-xs font-bold text-base-content/40 shrink-0">{{ strtoupper(substr($registeredUser->name, 0, 1)) }}</div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium truncate group-hover:text-primary transition-colors">{{ $registeredUser->name }}</p>
                                        <p class="text-xs text-base-content/40 truncate mt-0.5">{{ $registeredUser->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <x-ui.badge :variant="$roleVariant" size="xs">{{ $registeredUser->role->value }}</x-ui.badge>
                                    @if ($statusVariant)
                                        <x-ui.badge :variant="$statusVariant" size="xs">{{ ucfirst($registeredUser->status->value) }}</x-ui.badge>
                                    @endif
                                    <span class="text-xs text-base-content/30 hidden sm:block">{{ $registeredUser->created_at->format('M j, Y') }}</span>
                                </div>
                            </button>
                        @empty
                            <div class="py-10 flex flex-col items-center gap-2 text-center px-6">
                                <p class="text-sm text-base-content/40">No users found.</p>
                            </div>
                        @endforelse
                    </div>
                    @if ($users->hasPages())
                        <div class="px-5 py-4 border-t af-divider">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>

                {{-- Pending invitations --}}
                <div id="invitations-section" data-csrf="{{ csrf_token() }}" class="d d4 af-card overflow-hidden !p-0">
                    <div class="px-5 py-4 border-b af-divider flex items-center justify-between">
                        <h2 class="font-semibold text-sm">Pending Invitations</h2>
                        <span id="invitations-count" class="text-xs text-base-content/40">{{ $invitations->count() }}</span>
                    </div>
                    <div id="invitations-list" class="divide-y af-divider">
                        @forelse ($invitations as $invitation)
                            @php
                                $invRoleVariant = match ($invitation->role->value) {
                                    'Admin'   => 'primary',
                                    'Teacher' => 'secondary',
                                    default   => 'accent',
                                };
                            @endphp
                            <div data-invitation-id="{{ $invitation->id }}" class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-base-content/[.03] transition-colors">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium truncate">{{ $invitation->name ?? $invitation->email }}</p>
                                    @if ($invitation->name)
                                        <p class="text-xs text-base-content/40 mt-0.5 truncate">{{ $invitation->email }}</p>
                                    @else
                                        <p class="text-xs text-base-content/40 mt-0.5">Invited by {{ $invitation->inviter->name }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <x-ui.badge :variant="$invRoleVariant" size="xs">{{ $invitation->role->value }}</x-ui.badge>
                                    <span class="text-xs text-base-content/30 hidden sm:block">Expires {{ $invitation->expires_at->format('M j, Y') }}</span>
                                    <form method="POST" action="{{ route('admin.invitations.invalidate', $invitation) }}" onsubmit="return confirm('Invalidate this invitation?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center size-7 rounded-lg text-base-content/30 hover:text-error hover:bg-error/10 transition-colors" title="Invalidate">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-10 flex flex-col items-center gap-2 text-center px-6">
                                <p class="text-sm text-base-content/40">No pending invitations.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </main>
    </div>

    {{-- ─── User Management Modal ─────────────────────────────────────────── --}}
    <dialog id="user-modal" class="modal">
        <div class="af-modal-box modal-box w-full max-w-lg rounded-3xl p-0 overflow-hidden">
            {{-- Loading state --}}
            <div id="um-loading" class="flex items-center justify-center py-20">
                <span class="loading loading-spinner loading-md text-primary"></span>
            </div>

            {{-- Content (hidden until loaded) --}}
            <div id="um-content" class="hidden">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 border-b af-divider">
                    <div class="flex items-center gap-3 min-w-0">
                        <div id="um-avatar" class="size-10 rounded-full bg-base-200 flex items-center justify-center text-sm font-bold text-base-content/40 shrink-0"></div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 id="um-name" class="text-lg font-black tracking-tight truncate"></h3>
                                <span id="um-role-pill" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold"></span>
                                <span id="um-status-pill" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold"></span>
                            </div>
                            <p id="um-email" class="text-sm text-base-content/50 truncate"></p>
                        </div>
                    </div>
                    <form method="dialog">
                        <button class="btn btn-ghost btn-sm btn-square rounded-xl text-base-content/50" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </button>
                    </form>
                </div>

                {{-- Status Notice --}}
                <div id="um-status-notice" class="hidden mx-6 mt-5 rounded-xl border px-4 py-3">
                    <p id="um-status-notice-text" class="font-semibold text-sm"></p>
                    <p id="um-status-reason" class="text-sm text-base-content/60 mt-0.5 hidden"></p>
                </div>

                {{-- Account Actions --}}
                <div id="um-actions" class="hidden px-6 pt-5">
                    <p class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 mb-3">Account Actions</p>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" id="um-btn-block" onclick="openActionConfirm('block')" class="hidden inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-warning/10 text-warning border border-warning/20 text-sm font-semibold hover:bg-warning/15 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/><path d="M4.9 4.9 19.1 19.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            Block
                        </button>
                        <form id="um-form-unblock" method="POST" class="hidden">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-success/10 text-success border border-success/20 text-sm font-semibold hover:bg-success/15 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Restore
                            </button>
                        </form>
                        <button type="button" id="um-btn-archive" onclick="openActionConfirm('archive')" class="hidden inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-error/10 text-error border border-error/20 text-sm font-semibold hover:bg-error/15 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M3 6h18M8 6V4h8v2M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Archive
                        </button>
                        <a id="um-btn-profile" href="#" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-base-200 text-base-content/60 border border-base-300/50 text-sm font-medium hover:bg-base-300/50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Profile
                        </a>
                    </div>
                </div>

                {{-- Action confirm (inline) --}}
                <div id="um-action-confirm" class="hidden mx-6 mt-4 rounded-xl border p-4 space-y-3">
                    <p id="um-confirm-title" class="font-semibold text-sm"></p>
                    <p id="um-confirm-desc" class="text-xs text-base-content/50"></p>
                    <form id="um-confirm-form" method="POST">
                        @csrf
                        <textarea name="reason" rows="2" class="af-input" maxlength="500" placeholder="Reason (optional)..."></textarea>
                        <div class="flex gap-2 mt-2">
                            <button type="button" onclick="document.getElementById('um-action-confirm').classList.add('hidden')" class="flex-1 inline-flex justify-center items-center px-3 py-2 rounded-xl bg-base-200 text-base-content/60 border border-base-300/50 text-sm font-medium hover:bg-base-300/50 transition-colors">Cancel</button>
                            <button id="um-confirm-btn" type="submit" class="flex-1 inline-flex justify-center items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold transition-opacity hover:opacity-90">Confirm</button>
                        </div>
                    </form>
                </div>

                {{-- Edit Profile Form --}}
                <div class="px-6 py-5">
                    <p class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 mb-3">Edit Profile</p>
                    <form id="um-edit-form" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5">Full Name</label>
                                <input type="text" name="name" id="um-input-name" class="af-input" required>
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5">Email</label>
                                <input type="email" name="email" id="um-input-email" class="af-input" required>
                            </div>
                        </div>
                        <div id="um-guardian-fields" class="hidden mt-3 rounded-xl bg-base-200/50 border border-base-300/40 px-4 py-3 space-y-3">
                            <p class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35">Parent / Guardian</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5">Guardian Name</label>
                                    <input type="text" name="guardian_name" id="um-input-guardian-name" class="af-input" placeholder="Parent or guardian's name">
                                </div>
                                <div>
                                    <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5">Guardian Email</label>
                                    <input type="email" name="guardian_email" id="um-input-guardian-email" class="af-input" placeholder="parent@example.com">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="mt-3 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-primary-content text-sm font-semibold hover:opacity-90 transition-opacity">Save Changes</button>
                    </form>
                </div>

                {{-- Account Info --}}
                <div class="px-6 pb-6">
                    <div class="rounded-xl border border-base-300/50 overflow-hidden">
                        <div class="divide-y divide-base-300/30 text-sm">
                            <div class="flex items-center justify-between px-4 py-2.5">
                                <span class="text-xs font-medium text-base-content/50">Joined</span>
                                <span id="um-joined"></span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-2.5">
                                <span class="text-xs font-medium text-base-content/50">Email Verified</span>
                                <span id="um-verified"></span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-2.5">
                                <span class="text-xs font-medium text-base-content/50">Last Updated</span>
                                <span id="um-updated"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- ─── Invite Users Modal ────────────────────────────────────────────── --}}
    <dialog id="invite-modal" class="modal">
        <div class="af-modal-box modal-box w-full max-w-2xl rounded-3xl p-0 overflow-visible">
            {{-- Modal header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b af-divider">
                <div>
                    <h3 id="modal-title" class="text-lg font-black tracking-tight">Invite User</h3>
                    <p class="text-sm text-base-content/50 mt-0.5">Send an invitation email to a new user.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span id="count-badge" class="hidden items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-primary bg-primary/10 border-primary/20">1 invitee</span>
                    <form method="dialog">
                        <button class="btn btn-ghost btn-sm btn-square rounded-xl text-base-content/50" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Error list --}}
            @if ($errors->any())
                <div class="mx-6 mt-5 rounded-2xl border border-error/30 bg-error/5 px-4 py-3 space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm text-error">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form id="invite-form" method="POST" action="{{ route('admin.users.invite.send') }}" class="px-6 py-5 space-y-4">
                @csrf

                <div id="invitees-list" class="space-y-3">
                    <div class="invite-row rounded-xl border border-base-300/50 bg-base-200/30 p-4" data-index="0">
                        <div class="flex items-start gap-3">
                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="name-0">
                                        Full Name <span class="text-base-content/25 font-normal normal-case tracking-normal">(optional)</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="name-0"
                                        name="invitees[0][name]"
                                        value="{{ old('invitees.0.name') }}"
                                        class="af-input {{ $errors->has('invitees.0.name') ? 'af-input-error' : '' }}"
                                        placeholder="Their name"
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
                                        placeholder="email@example.com"
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
                                class="remove-row-btn inline-flex items-center justify-center size-8 rounded-xl text-base-content/30 hover:text-error hover:bg-error/10 mt-6 hidden shrink-0 transition-colors"
                                aria-label="Remove"
                                onclick="removeRow(this)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    id="add-row-btn"
                    onclick="addRow()"
                    class="w-full flex items-center justify-center gap-2 rounded-xl border border-dashed border-base-300/70 bg-transparent hover:border-primary hover:text-primary hover:bg-primary/5 py-2.5 text-sm text-base-content/40 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Add another invitee
                </button>

                <div class="flex items-center justify-end gap-3 pt-1 border-t af-divider">
                    <form method="dialog" class="inline">
                        <button class="inline-flex items-center px-4 py-2.5 rounded-xl bg-base-200 text-base-content/60 border border-base-300/50 text-sm font-medium hover:bg-base-300/50 transition-colors">
                            Cancel
                        </button>
                    </form>
                    <button type="submit" id="submit-btn" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-primary-content text-sm font-semibold hover:opacity-90 transition-opacity">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Send Invitation
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    <script>
        // ── User Management Modal ──────────────────────────────────────────
        let currentUser = null;

        function esc(str) {
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function rolePillClass(role) {
            if (role === 'Admin')   return 'text-primary bg-primary/10 border-primary/20';
            if (role === 'Teacher') return 'text-secondary bg-secondary/10 border-secondary/20';
            return 'text-accent bg-accent/10 border-accent/20';
        }

        function statusPillClass(status) {
            if (status === 'blocked')  return 'text-warning bg-warning/10 border-warning/20';
            if (status === 'archived') return 'text-error bg-error/10 border-error/20';
            return 'text-success bg-success/10 border-success/20';
        }

        function openUserModal(userId) {
            const modal = document.getElementById('user-modal');
            const loading = document.getElementById('um-loading');
            const content = document.getElementById('um-content');

            loading.classList.remove('hidden');
            content.classList.add('hidden');
            document.getElementById('um-action-confirm').classList.add('hidden');
            document.getElementById('um-confirm-form').querySelector('textarea').value = '';
            modal.showModal();

            fetch(`/admin/users/${userId}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(user => {
                currentUser = user;

                const av = document.getElementById('um-avatar');
                if (user.avatar_url) {
                    av.innerHTML = `<img src="${esc(user.avatar_url)}" class="size-10 rounded-full object-cover" alt="">`;
                } else {
                    av.textContent = user.name.charAt(0).toUpperCase();
                }

                document.getElementById('um-name').textContent = user.name;
                document.getElementById('um-email').textContent = user.email;

                const rp = document.getElementById('um-role-pill');
                rp.textContent = user.role;
                rp.className = 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold ' + rolePillClass(user.role);

                const sp = document.getElementById('um-status-pill');
                sp.textContent = user.status.charAt(0).toUpperCase() + user.status.slice(1);
                sp.className = 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold ' + statusPillClass(user.status);

                const notice = document.getElementById('um-status-notice');
                const noticeText = document.getElementById('um-status-notice-text');
                const reasonEl = document.getElementById('um-status-reason');
                if (user.status === 'blocked' || user.status === 'archived') {
                    notice.classList.remove('hidden');
                    notice.className = 'mx-6 mt-5 rounded-xl border px-4 py-3 ' + (user.status === 'archived' ? 'border-error/20 bg-error/5' : 'border-warning/20 bg-warning/5');
                    noticeText.textContent = user.status === 'archived' ? 'Account Archived' : 'Account Blocked';
                    noticeText.className = 'font-semibold text-sm ' + (user.status === 'archived' ? 'text-error' : 'text-warning');
                    if (user.status_reason) { reasonEl.textContent = user.status_reason; reasonEl.classList.remove('hidden'); }
                    else { reasonEl.classList.add('hidden'); }
                } else {
                    notice.classList.add('hidden');
                }

                const actions = document.getElementById('um-actions');
                if (user.is_self) {
                    actions.classList.add('hidden');
                } else {
                    actions.classList.remove('hidden');
                    document.getElementById('um-btn-block').classList.toggle('hidden', user.status !== 'active');
                    document.getElementById('um-form-unblock').classList.toggle('hidden', user.status !== 'blocked');
                    document.getElementById('um-form-unblock').action = user.unblock_url;
                    document.getElementById('um-btn-archive').classList.toggle('hidden', user.status === 'archived');
                    document.getElementById('um-btn-profile').href = user.profile_url;
                }

                document.getElementById('um-edit-form').action = user.update_url;
                document.getElementById('um-input-name').value = user.name;
                document.getElementById('um-input-email').value = user.email;

                const guardianFields = document.getElementById('um-guardian-fields');
                if (user.is_student) {
                    guardianFields.classList.remove('hidden');
                    document.getElementById('um-input-guardian-name').value = user.guardian_name || '';
                    document.getElementById('um-input-guardian-email').value = user.guardian_email || '';
                } else {
                    guardianFields.classList.add('hidden');
                }

                document.getElementById('um-joined').textContent = user.created_at;
                document.getElementById('um-verified').textContent = user.email_verified_at || '—';
                document.getElementById('um-updated').textContent = user.updated_at;

                loading.classList.add('hidden');
                content.classList.remove('hidden');
            })
            .catch(() => { modal.close(); });
        }

        function openActionConfirm(action) {
            const confirm = document.getElementById('um-action-confirm');
            const form = document.getElementById('um-confirm-form');
            const title = document.getElementById('um-confirm-title');
            const desc = document.getElementById('um-confirm-desc');
            const btn = document.getElementById('um-confirm-btn');

            if (action === 'block') {
                form.action = currentUser.block_url;
                title.textContent = 'Block Account';
                title.className = 'font-semibold text-sm text-warning';
                desc.textContent = 'This user will be logged out and unable to access the system until restored.';
                btn.className = 'flex-1 inline-flex justify-center items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold bg-warning text-warning-content transition-opacity hover:opacity-90';
                btn.textContent = 'Block';
                confirm.className = 'mx-6 mt-4 rounded-xl border border-warning/30 bg-warning/5 p-4 space-y-3';
            } else {
                form.action = currentUser.archive_url;
                title.textContent = 'Archive Account';
                title.className = 'font-semibold text-sm text-error';
                desc.textContent = 'This will permanently deactivate the account.';
                btn.className = 'flex-1 inline-flex justify-center items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold bg-error text-error-content transition-opacity hover:opacity-90';
                btn.textContent = 'Archive';
                confirm.className = 'mx-6 mt-4 rounded-xl border border-error/30 bg-error/5 p-4 space-y-3';
            }

            confirm.classList.remove('hidden');
        }

        // ── Invite Modal ──────────────────────────────────────────────────
        const invList = document.getElementById('invitees-list');
        const submitBtn = document.getElementById('submit-btn');
        const modalTitle = document.getElementById('modal-title');
        const countBadge = document.getElementById('count-badge');

        function rowCount() { return invList.querySelectorAll('.invite-row').length; }

        function updateUI() {
            const count = rowCount();
            const isBulk = count > 1;
            modalTitle.textContent = isBulk ? `Invite ${count} Users` : 'Invite User';
            countBadge.textContent = count + (count === 1 ? ' invitee' : ' invitees');
            countBadge.classList.toggle('hidden', !isBulk);
            countBadge.classList.toggle('inline-flex', isBulk);
            submitBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg> ${isBulk ? `Send ${count} Invitations` : 'Send Invitation'}`;
            invList.querySelectorAll('.remove-row-btn').forEach(btn => btn.classList.toggle('hidden', !isBulk));
            invList.querySelectorAll('.invite-row').forEach((row, i) => {
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
            const clone = invList.querySelector('.invite-row').cloneNode(true);
            clone.querySelector('input[type="text"]').value = '';
            clone.querySelector('input[type="email"]').value = '';
            clone.querySelector('select').selectedIndex = 0;
            invList.appendChild(clone);
            updateUI();
            clone.querySelector('input[type="text"]').focus();
        }

        function removeRow(btn) {
            if (rowCount() <= 1) return;
            btn.closest('.invite-row').remove();
            updateUI();
        }

        updateUI();

        @if ($errors->any())
            document.getElementById('invite-modal').showModal();
        @endif

        // ── Real-time invitation polling ──────────────────────────────────────
        (function () {
            const section = document.getElementById('invitations-section');
            if (!section) return;

            const countEl  = document.getElementById('invitations-count');
            const listEl   = document.getElementById('invitations-list');
            const csrf     = section.dataset.csrf;
            const endpoint = '{{ route('admin.invitations.pending') }}';

            function invRolePillClass(role) {
                if (role === 'Admin')   return 'text-primary bg-primary/10 border-primary/20';
                if (role === 'Teacher') return 'text-secondary bg-secondary/10 border-secondary/20';
                return 'text-accent bg-accent/10 border-accent/20';
            }

            function buildList(items) {
                if (!items.length) {
                    return '<div class="py-10 flex flex-col items-center gap-2 text-center px-6"><p class="text-sm text-base-content/40">No pending invitations.</p></div>';
                }
                return items.map(inv => {
                    const pill    = invRolePillClass(inv.role);
                    const subLine = inv.has_name
                        ? `<p class="text-xs text-base-content/40 mt-0.5 truncate">${esc(inv.email)}</p>`
                        : `<p class="text-xs text-base-content/40 mt-0.5">Invited by ${esc(inv.inviter_name)}</p>`;
                    return `<div data-invitation-id="${esc(inv.id)}" class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-base-200/40 transition-colors">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium truncate">${esc(inv.display_name)}</p>
                            ${subLine}
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold ${pill}">${esc(inv.role)}</span>
                            <span class="text-xs text-base-content/30 hidden sm:block">Expires ${esc(inv.expires_at)}</span>
                            <form method="POST" action="${esc(inv.invalidate_url)}" onsubmit="return confirm('Invalidate this invitation?')">
                                <input type="hidden" name="_token" value="${esc(csrf)}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="inline-flex items-center justify-center size-7 rounded-lg text-base-content/30 hover:text-error hover:bg-error/10 transition-colors" title="Invalidate">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>`;
                }).join('');
            }

            function currentIds() {
                return Array.from(listEl.querySelectorAll('[data-invitation-id]'))
                    .map(el => el.dataset.invitationId);
            }

            async function poll() {
                try {
                    const res = await fetch(endpoint, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    const newIds  = data.items.map(i => i.id);
                    const prevIds = currentIds();
                    const changed = prevIds.length !== newIds.length ||
                                    prevIds.some((id, idx) => id !== newIds[idx]);
                    if (changed) {
                        listEl.innerHTML  = buildList(data.items);
                        countEl.textContent = data.count;
                    }
                } catch (_) {}
            }

            setInterval(poll, 20000);
        })();
    </script>

</x-layouts.app>
