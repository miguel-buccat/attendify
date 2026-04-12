<x-layouts.app :title="'User — ' . $user->name">
    <style>
        @keyframes d-up { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        @keyframes scale-in { from { opacity: 0; transform: scale(.96); } to { opacity: 1; transform: none; } }
        .d { animation: d-up .45s cubic-bezier(.16,1,.3,1) both; }
        .d1 { animation-delay: .00s; } .d2 { animation-delay: .07s; } .d3 { animation-delay: .14s; }
        .d4 { animation-delay: .21s; } .d5 { animation-delay: .28s; }
        /* Modal */
        #confirm-modal { display: none; }
        #confirm-modal.open { display: flex; }
        @keyframes modal-in { from { opacity: 0; transform: scale(.94) translateY(12px); } to { opacity: 1; transform: none; } }
        #confirm-modal.open > div { animation: modal-in .3s cubic-bezier(.16,1,.3,1) both; }
    </style>
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="users" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8">
                <div class="max-w-2xl space-y-4">

                    @if (session('success'))
                        <x-alert type="success" :message="session('success')" />
                    @endif

                    {{-- Header --}}
                    <div class="d d1">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/40 hover:text-base-content transition-colors mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Back to Users
                        </a>
                        <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Admin · User</p>
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight">{{ $user->name }}</h1>
                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                            @php
                                $rolePill = match ($user->role->value) {
                                    'Admin'   => 'text-primary bg-primary/10 border-primary/20',
                                    'Teacher' => 'text-secondary bg-secondary/10 border-secondary/20',
                                    default   => 'text-accent bg-accent/10 border-accent/20',
                                };
                                $statusPill = match ($user->status->value) {
                                    'blocked'  => 'text-warning bg-warning/10 border-warning/20',
                                    'archived' => 'text-error bg-error/10 border-error/20',
                                    default    => 'text-success bg-success/10 border-success/20',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $rolePill }}">{{ $user->role->value }}</span>
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusPill }}">{{ ucfirst($user->status->value) }}</span>
                            @if ($user->email_verified_at)
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-success bg-success/10 border-success/20">Verified</span>
                            @endif
                        </div>
                    </div>

                    {{-- Status notice --}}
                    @if ($user->isBlocked() || $user->isArchived())
                        <div class="d d2 rounded-2xl border {{ $user->isArchived() ? 'border-error/20 bg-error/5' : 'border-warning/20 bg-warning/5' }} px-5 py-4">
                            <p class="font-semibold text-sm {{ $user->isArchived() ? 'text-error' : 'text-warning' }}">
                                {{ $user->isArchived() ? 'Account Archived' : 'Account Temporarily Blocked' }}
                            </p>
                            @if ($user->status_reason)
                                <p class="text-sm text-base-content/60 mt-0.5">{{ $user->status_reason }}</p>
                            @endif
                            @if ($user->isBlocked())
                                <form method="POST" action="{{ route('admin.users.unblock', $user) }}" class="mt-3">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-success/10 text-success border border-success/20 text-sm font-semibold hover:bg-success/15 transition-colors">
                                        Restore Access
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    {{-- Account Actions --}}
                    @if ($user->id !== auth()->id())
                        <div class="d d2 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                            <div class="px-5 py-4 border-b border-base-300/30">
                                <h2 class="font-semibold text-sm">Account Actions</h2>
                            </div>
                            <div class="px-5 py-4 flex flex-wrap gap-2">
                                @if ($user->isActive())
                                    <button
                                        type="button"
                                        onclick="openBlockModal()"
                                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-warning/10 text-warning border border-warning/20 text-sm font-semibold hover:bg-warning/15 transition-colors"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/><path d="M4.9 4.9 19.1 19.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                        Temporarily Block
                                    </button>
                                @elseif ($user->isBlocked())
                                    <form method="POST" action="{{ route('admin.users.unblock', $user) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-success/10 text-success border border-success/20 text-sm font-semibold hover:bg-success/15 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            Restore Access
                                        </button>
                                    </form>
                                @endif

                                @if (! $user->isArchived())
                                    <button
                                        type="button"
                                        onclick="openArchiveModal()"
                                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-error/10 text-error border border-error/20 text-sm font-semibold hover:bg-error/15 transition-colors"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M3 6h18M8 6V4h8v2M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Archive Account
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Edit Profile --}}
                    <div class="d d3 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                        <div class="px-5 py-4 border-b border-base-300/30">
                            <h2 class="font-semibold text-sm">Profile Details</h2>
                        </div>
                        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="px-5 py-5 space-y-4">
                            @csrf
                            @method('PATCH')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="name">Full Name</label>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="{{ old('name', $user->name) }}"
                                        class="w-full rounded-xl border {{ $errors->has('name') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                                        required
                                    >
                                    @error('name')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="email">Email</label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="{{ old('email', $user->email) }}"
                                        class="w-full rounded-xl border {{ $errors->has('email') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                                        required
                                    >
                                    @error('email')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="about_me">About</label>
                                <textarea
                                    id="about_me"
                                    name="about_me"
                                    rows="3"
                                    class="w-full rounded-xl border {{ $errors->has('about_me') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                                    maxlength="1000"
                                >{{ old('about_me', $user->about_me) }}</textarea>
                                @error('about_me')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
                            </div>

                            @if ($user->role === \App\Enums\UserRole::Student)
                                <div class="rounded-xl bg-base-200/50 border border-base-300/40 px-4 py-3 space-y-3">
                                    <p class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35">Parent / Guardian</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="guardian_email">Guardian Email</label>
                                            <input
                                                type="email"
                                                id="guardian_email"
                                                name="guardian_email"
                                                value="{{ old('guardian_email', $user->guardian_email) }}"
                                                class="w-full rounded-xl border {{ $errors->has('guardian_email') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                                                placeholder="parent@example.com"
                                            >
                                            @error('guardian_email')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="guardian_phone">Guardian Phone</label>
                                            <input
                                                type="tel"
                                                id="guardian_phone"
                                                name="guardian_phone"
                                                value="{{ old('guardian_phone', $user->guardian_phone) }}"
                                                class="w-full rounded-xl border {{ $errors->has('guardian_phone') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                                                placeholder="+1 234 567 8900"
                                            >
                                            @error('guardian_phone')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center gap-3 pt-1">
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-primary-content text-sm font-semibold hover:opacity-90 transition-opacity">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Account Info --}}
                    <div class="d d4 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                        <div class="px-5 py-4 border-b border-base-300/30">
                            <h2 class="font-semibold text-sm">Account Info</h2>
                        </div>
                        <div class="divide-y divide-base-300/30">
                            <div class="flex items-center justify-between px-5 py-3">
                                <span class="text-xs font-medium text-base-content/50">Joined</span>
                                <span class="text-sm">{{ $user->created_at->format('F j, Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between px-5 py-3">
                                <span class="text-xs font-medium text-base-content/50">Email Verified</span>
                                <span class="text-sm">{{ $user->email_verified_at ? $user->email_verified_at->format('F j, Y') : '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between px-5 py-3">
                                <span class="text-xs font-medium text-base-content/50">Last Updated</span>
                                <span class="text-sm">{{ $user->updated_at->format('F j, Y, g:i A') }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    {{-- Block Modal --}}
    <div id="confirm-modal" class="fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="w-full max-w-sm bg-base-100 rounded-3xl border border-base-300/50 shadow-2xl p-6 space-y-4">
            <div id="modal-icon" class="size-12 rounded-2xl flex items-center justify-center mx-auto"></div>
            <div class="text-center">
                <h3 id="modal-title" class="font-black text-lg tracking-tight"></h3>
                <p id="modal-desc" class="text-sm text-base-content/50 mt-1"></p>
            </div>
            <form id="modal-form" method="POST" class="space-y-3">
                @csrf
                <span id="modal-method-field"></span>
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="modal-reason">Reason <span class="normal-case font-normal">(optional)</span></label>
                    <textarea id="modal-reason" name="reason" rows="2" class="w-full rounded-xl border border-base-300/70 bg-base-100 px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40" maxlength="500" placeholder="Optional reason..."></textarea>
                </div>
                <div class="flex gap-2 pt-1">
                    <button type="button" onclick="closeModal()" class="flex-1 inline-flex justify-center items-center px-4 py-2.5 rounded-xl bg-base-200 text-base-content/60 border border-base-300/50 text-sm font-medium hover:bg-base-300/50 transition-colors">
                        Cancel
                    </button>
                    <button id="modal-confirm-btn" type="submit" class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-opacity hover:opacity-90">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openBlockModal() {
            const modal = document.getElementById('confirm-modal');
            const form = document.getElementById('modal-form');
            const icon = document.getElementById('modal-icon');
            const title = document.getElementById('modal-title');
            const desc = document.getElementById('modal-desc');
            const methodField = document.getElementById('modal-method-field');
            const confirmBtn = document.getElementById('modal-confirm-btn');

            form.action = '{{ route('admin.users.block', $user) }}';
            methodField.innerHTML = '<input type="hidden" name="_method" value="POST">';
            icon.className = 'size-12 rounded-2xl flex items-center justify-center mx-auto bg-warning/10';
            icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-6 text-warning"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/><path d="M4.9 4.9 19.1 19.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
            title.textContent = 'Temporarily Block Account';
            desc.textContent = 'This user will be logged out and unable to access the system until restored.';
            confirmBtn.className = 'flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-warning text-warning-content transition-opacity hover:opacity-90';
            confirmBtn.textContent = 'Block Account';

            modal.classList.add('open');
        }

        function openArchiveModal() {
            const modal = document.getElementById('confirm-modal');
            const form = document.getElementById('modal-form');
            const icon = document.getElementById('modal-icon');
            const title = document.getElementById('modal-title');
            const desc = document.getElementById('modal-desc');
            const methodField = document.getElementById('modal-method-field');
            const confirmBtn = document.getElementById('modal-confirm-btn');

            form.action = '{{ route('admin.users.archive', $user) }}';
            methodField.innerHTML = '<input type="hidden" name="_method" value="POST">';
            icon.className = 'size-12 rounded-2xl flex items-center justify-center mx-auto bg-error/10';
            icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-6 text-error"><path d="M3 6h18M8 6V4h8v2M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            title.textContent = 'Archive Account';
            desc.textContent = 'This will permanently deactivate the account. This action cannot be undone.';
            confirmBtn.className = 'flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-error text-error-content transition-opacity hover:opacity-90';
            confirmBtn.textContent = 'Archive Permanently';

            modal.classList.add('open');
        }

        function closeModal() {
            document.getElementById('confirm-modal').classList.remove('open');
            document.getElementById('modal-reason').value = '';
        }

        // Close on backdrop click
        document.getElementById('confirm-modal').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });

        // Close on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });
    </script>
</x-layouts.app>
