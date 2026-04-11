<x-layouts.app title="Invite User">
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
                <div class="max-w-lg">
                    <div class="mb-6">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/60 hover:text-base-content transition-colors mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back to Users
                        </a>
                        <h1 class="text-2xl md:text-3xl font-semibold">Invite User</h1>
                        <p class="mt-1 text-base-content/60">Send an invitation email to a new teacher or student.</p>
                    </div>

                    <div class="rounded-xl border border-base-300 bg-base-100 p-6">
                        <form method="POST" action="{{ route('admin.users.invite.send') }}" class="space-y-5">
                            @csrf

                            <div class="form-control">
                                <label class="label" for="email">
                                    <span class="label-text font-medium">Email Address</span>
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="input input-bordered w-full rounded-xl @error('email') input-error @enderror"
                                    placeholder="invitee@example.com"
                                    required
                                    autofocus
                                >
                                @error('email')
                                    <div class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label" for="role">
                                    <span class="label-text font-medium">Role</span>
                                </label>
                                <select
                                    id="role"
                                    name="role"
                                    class="select select-bordered w-full rounded-xl @error('role') select-error @enderror"
                                    required
                                >
                                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select a role</option>
                                    <option value="Teacher" {{ old('role') === 'Teacher' ? 'selected' : '' }}>Teacher</option>
                                    <option value="Student" {{ old('role') === 'Student' ? 'selected' : '' }}>Student</option>
                                </select>
                                @error('role')
                                    <div class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <div class="flex items-center gap-3 pt-2">
                                <button type="submit" class="btn btn-primary rounded-xl normal-case">
                                    Send Invitation
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost rounded-xl normal-case">Cancel</a>
                            </div>
                        </form>
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
