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
                    <a href="{{ route('admin.users.invite') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-primary-content text-sm font-semibold hover:opacity-90 transition-opacity self-start">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Invite User
                    </a>
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
                <div class="d d3 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-base-300/30 flex items-center justify-between">
                        <h2 class="font-semibold text-sm">Pending Invitations</h2>
                        <span class="text-xs text-base-content/40">{{ $invitations->count() }}</span>
                    </div>
                    <div class="divide-y divide-base-300/30">
                        @forelse ($invitations as $invitation)
                            @php
                                $invRolePill = match ($invitation->role->value) {
                                    'Teacher' => 'text-secondary bg-secondary/10 border-secondary/20',
                                    default   => 'text-accent bg-accent/10 border-accent/20',
                                };
                            @endphp
                            <div class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-base-200/40 transition-colors">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium truncate">{{ $invitation->email }}</p>
                                    <p class="text-xs text-base-content/40 mt-0.5">Invited by {{ $invitation->inviter->name }}</p>
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

</x-layouts.app>
