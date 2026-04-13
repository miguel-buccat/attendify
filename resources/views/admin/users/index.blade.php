<x-layouts.app title="Manage Users">
    <style>
        @keyframes d-up { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .d { animation: d-up .45s cubic-bezier(.16,1,.3,1) both; }
        .d1 { animation-delay: .00s; } .d2 { animation-delay: .07s; } .d3 { animation-delay: .14s; }
        .d4 { animation-delay: .21s; }
    </style>
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

                {{-- Users list --}}
                <div class="d d2 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-base-300/30 flex items-center justify-between">
                        <h2 class="font-semibold text-sm">Registered Users</h2>
                        <span class="text-xs text-base-content/40">{{ $users->count() }}</span>
                    </div>
                    <div class="divide-y divide-base-300/30">
                        @forelse ($users as $registeredUser)
                            @php
                                $rolePill = match ($registeredUser->role->value) {
                                    'Admin'   => 'text-primary bg-primary/10 border-primary/20',
                                    'Teacher' => 'text-secondary bg-secondary/10 border-secondary/20',
                                    default   => 'text-accent bg-accent/10 border-accent/20',
                                };
                                $statusPill = match ($registeredUser->status->value) {
                                    'blocked'  => 'text-warning bg-warning/10 border-warning/20',
                                    'archived' => 'text-error bg-error/10 border-error/20',
                                    default    => null,
                                };
                            @endphp
                            <a href="{{ route('admin.users.show', $registeredUser) }}" class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-base-200/40 transition-colors group">
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
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $rolePill }}">{{ $registeredUser->role->value }}</span>
                                    @if ($statusPill)
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusPill }}">{{ ucfirst($registeredUser->status->value) }}</span>
                                    @endif
                                    <span class="text-xs text-base-content/30 hidden sm:block">{{ $registeredUser->created_at->format('M j, Y') }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="py-10 flex flex-col items-center gap-2 text-center px-6">
                                <p class="text-sm text-base-content/40">No users registered yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Pending invitations --}}
                <div id="invitations-section" data-csrf="{{ csrf_token() }}" class="d d3 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-base-300/30 flex items-center justify-between">
                        <h2 class="font-semibold text-sm">Pending Invitations</h2>
                        <span id="invitations-count" class="text-xs text-base-content/40">{{ $invitations->count() }}</span>
                    </div>
                    <div id="invitations-list" class="divide-y divide-base-300/30">
                        @forelse ($invitations as $invitation)
                            @php
                                $invRolePill = match ($invitation->role->value) {
                                    'Admin'   => 'text-primary bg-primary/10 border-primary/20',
                                    'Teacher' => 'text-secondary bg-secondary/10 border-secondary/20',
                                    default   => 'text-accent bg-accent/10 border-accent/20',
                                };
                            @endphp
                            <div data-invitation-id="{{ $invitation->id }}" class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-base-200/40 transition-colors">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium truncate">{{ $invitation->name ?? $invitation->email }}</p>
                                    @if ($invitation->name)
                                        <p class="text-xs text-base-content/40 mt-0.5 truncate">{{ $invitation->email }}</p>
                                    @else
                                        <p class="text-xs text-base-content/40 mt-0.5">Invited by {{ $invitation->inviter->name }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $invRolePill }}">{{ $invitation->role->value }}</span>
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

    {{-- ─── Invite Users Modal ────────────────────────────────────────────── --}}
    <dialog id="invite-modal" class="modal">
        <div class="modal-box w-full max-w-2xl rounded-3xl p-0 overflow-visible">
            {{-- Modal header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-base-300/40">
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
                                        class="w-full rounded-xl border border-base-300/70 bg-base-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 {{ $errors->has('invitees.0.name') ? 'border-error' : '' }}"
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
                                        class="w-full rounded-xl border border-base-300/70 bg-base-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 {{ $errors->has('invitees.0.email') ? 'border-error' : '' }}"
                                        placeholder="email@example.com"
                                        required
                                    >
                                </div>
                                <div>
                                    <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="role-0">Role</label>
                                    <select
                                        id="role-0"
                                        name="invitees[0][role]"
                                        class="w-full rounded-xl border border-base-300/70 bg-base-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 {{ $errors->has('invitees.0.role') ? 'border-error' : '' }}"
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

                <div class="flex items-center justify-end gap-3 pt-1 border-t border-base-300/30">
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

            function esc(str) {
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            }

            function rolePillClass(role) {
                if (role === 'Admin')   return 'text-primary bg-primary/10 border-primary/20';
                if (role === 'Teacher') return 'text-secondary bg-secondary/10 border-secondary/20';
                return 'text-accent bg-accent/10 border-accent/20';
            }

            function buildList(items) {
                if (!items.length) {
                    return '<div class="py-10 flex flex-col items-center gap-2 text-center px-6"><p class="text-sm text-base-content/40">No pending invitations.</p></div>';
                }
                return items.map(inv => {
                    const pill    = rolePillClass(inv.role);
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
