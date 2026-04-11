<x-layouts.app title="Manage Users">
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
            <div class="p-4 md:p-8 space-y-6">

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-semibold">Manage Users</h1>
                        <p class="mt-1 text-base-content/60">All registered users and pending invitations</p>
                    </div>
                    <a href="{{ route('admin.users.invite') }}" class="btn btn-primary rounded-xl gap-2 normal-case">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Invite User
                    </a>
                </div>

                {{-- Users Table --}}
                <div class="rounded-xl border border-base-300 bg-base-100 overflow-hidden">
                    <div class="px-4 py-3 border-b border-base-300 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 text-base-content/60" aria-hidden="true">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h2 class="font-semibold text-sm">Registered Users</h2>
                        <span class="badge badge-ghost badge-sm ml-auto">{{ $users->count() }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $registeredUser)
                                    <tr>
                                        <td class="font-medium">{{ $registeredUser->name }}</td>
                                        <td class="text-base-content/70">{{ $registeredUser->email }}</td>
                                        <td>
                                            <span @class([
                                                'badge badge-sm',
                                                'badge-primary' => $registeredUser->role->value === 'Admin',
                                                'badge-secondary' => $registeredUser->role->value === 'Teacher',
                                                'badge-accent' => $registeredUser->role->value === 'Student',
                                            ])>{{ $registeredUser->role->value }}</span>
                                        </td>
                                        <td>
                                            @if ($registeredUser->email_verified_at)
                                                <span class="badge badge-success badge-sm badge-outline">Verified</span>
                                            @else
                                                <span class="badge badge-warning badge-sm badge-outline">Unverified</span>
                                            @endif
                                        </td>
                                        <td class="text-base-content/60 text-xs">{{ $registeredUser->created_at->format('M j, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-base-content/50 py-6">No users registered yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pending Invitations Table --}}
                <div class="rounded-xl border border-base-300 bg-base-100 overflow-hidden">
                    <div class="px-4 py-3 border-b border-base-300 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 text-base-content/60" aria-hidden="true">
                            <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2ZM2 8l10 7 10-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h2 class="font-semibold text-sm">Pending Invitations</h2>
                        <span class="badge badge-ghost badge-sm ml-auto">{{ $invitations->count() }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Invited By</th>
                                    <th>Expires</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invitations as $invitation)
                                    <tr>
                                        <td class="font-medium">{{ $invitation->email }}</td>
                                        <td>
                                            <span @class([
                                                'badge badge-sm',
                                                'badge-secondary' => $invitation->role->value === 'Teacher',
                                                'badge-accent' => $invitation->role->value === 'Student',
                                            ])>{{ $invitation->role->value }}</span>
                                        </td>
                                        <td class="text-base-content/70">{{ $invitation->inviter->name }}</td>
                                        <td class="text-base-content/60 text-xs">{{ $invitation->expires_at->format('M j, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-base-content/50 py-6">No pending invitations.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
</x-layouts.app>
