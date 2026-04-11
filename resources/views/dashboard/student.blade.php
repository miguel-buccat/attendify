<x-layouts.app title="Student Dashboard">
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
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-primary/10 text-primary font-medium text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                        <path d="M4 11.5 12 5l8 6.5V20a1 1 0 0 1-1 1h-4v-6H9v6H5a1 1 0 0 1-1-1v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Dashboard
                </a>

                {{-- Added in Phase 4 --}}
                {{-- <a href="{{ route('student.classes.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-base-200 text-base-content/80 text-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                        <path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    My Classes
                </a> --}}

                {{-- Added in Phase 5 --}}
                {{-- <a href="{{ route('student.scan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-base-200 text-base-content/80 text-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                        <path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2m5-5h4m-2-2v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Scan QR
                </a>
                <a href="{{ route('student.attendance') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-base-200 text-base-content/80 text-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                        <path d="M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    My Attendance
                </a> --}}
            </nav>

            <div class="p-3 border-t border-base-300">
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-base-200 transition-colors">
                    <span class="inline-flex items-center justify-center size-8 rounded-lg bg-primary/15 text-primary text-xs font-bold shrink-0">
                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-medium truncate">{{ $user->name }}</p>
                        <p class="text-xs text-base-content/60">Student</p>
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
                <span class="badge badge-accent badge-sm">Student</span>
                <button type="button" onclick="document.getElementById('logout-modal').showModal()" class="btn btn-ghost btn-xs rounded-lg">Logout</button>
            </div>
        </div>

        {{-- Main content --}}
        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-semibold">Student Dashboard</h1>
                        <p class="mt-1 text-base-content/60">Welcome back, {{ $user->name }}</p>
                    </div>
                    <span class="badge badge-accent badge-lg hidden lg:inline-flex">Student</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    <article class="rounded-xl border border-base-300 bg-base-100 p-4">
                        <p class="text-xs uppercase tracking-wider text-base-content/60">My Classes</p>
                        <p class="mt-2 text-3xl font-bold">—</p>
                    </article>
                    <article class="rounded-xl border border-base-300 bg-base-100 p-4">
                        <p class="text-xs uppercase tracking-wider text-base-content/60">Sessions Attended</p>
                        <p class="mt-2 text-3xl font-bold">—</p>
                    </article>
                    <article class="rounded-xl border border-base-300 bg-base-100 p-4">
                        <p class="text-xs uppercase tracking-wider text-base-content/60">Attendance Rate</p>
                        <p class="mt-2 text-3xl font-bold">—</p>
                    </article>
                </div>

                <div class="rounded-xl border border-dashed border-base-300 bg-base-100 p-8 text-center text-base-content/50">
                    Analytics charts coming in Phase 7
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
