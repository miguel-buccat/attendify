<x-layouts.app title="Manage Users">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="users" />

        {{-- Main content --}}
        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold">Manage Users</h1>
                        <p class="mt-1 text-sm text-base-content/60">All registered users and pending invitations</p>
                    </div>
                    <a href="{{ route('admin.users.invite') }}" class="btn btn-primary btn-sm sm:btn-md rounded-xl gap-2 normal-case self-start sm:self-auto shrink-0">
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

                    {{-- Mobile: card layout --}}
                    <div class="sm:hidden divide-y divide-base-200">
                        @forelse ($users as $registeredUser)
                            <div class="flex items-center justify-between gap-3 px-4 py-3">
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium truncate">{{ $registeredUser->name }}</p>
                                    <p class="text-xs text-base-content/50 truncate mt-0.5">{{ $registeredUser->email }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span @class([
                                        'badge badge-sm',
                                        'badge-primary' => $registeredUser->role->value === 'Admin',
                                        'badge-secondary' => $registeredUser->role->value === 'Teacher',
                                        'badge-accent' => $registeredUser->role->value === 'Student',
                                    ])>{{ $registeredUser->role->value }}</span>
                                    @if ($registeredUser->email_verified_at)
                                        <span class="badge badge-success badge-sm badge-outline">Verified</span>
                                    @else
                                        <span class="badge badge-warning badge-sm badge-outline">Unverified</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-base-content/50 py-6">No users registered yet.</p>
                        @endforelse
                    </div>

                    {{-- Desktop: table layout --}}
                    <div class="hidden sm:block overflow-x-auto">
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

                    {{-- Mobile: card layout --}}
                    <div class="sm:hidden divide-y divide-base-200">
                        @forelse ($invitations as $invitation)
                            <div class="flex items-center justify-between gap-3 px-4 py-3">
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium truncate">{{ $invitation->email }}</p>
                                    <p class="text-xs text-base-content/50 mt-0.5">Invited by {{ $invitation->inviter->name }}</p>
                                </div>
                                <span @class([
                                    'badge badge-sm shrink-0',
                                    'badge-secondary' => $invitation->role->value === 'Teacher',
                                    'badge-accent' => $invitation->role->value === 'Student',
                                ])>{{ $invitation->role->value }}</span>
                            </div>
                        @empty
                            <p class="text-center text-base-content/50 py-6">No pending invitations.</p>
                        @endforelse
                    </div>

                    {{-- Desktop: table layout --}}
                    <div class="hidden sm:block overflow-x-auto">
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

</x-layouts.app>
