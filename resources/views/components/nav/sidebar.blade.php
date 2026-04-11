@props(['active' => null])

@php
    $user = auth()->user();
    $role = $user->role->value; // 'Admin', 'Teacher', 'Student'

    // Badge colour per role
    $roleBadgeClass = match ($role) {
        'Admin'   => 'badge-primary',
        'Teacher' => 'badge-secondary',
        default   => 'badge-accent',
    };

    // Helper: returns the correct active/inactive nav link classes
    $navClass = fn (string $key) => 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors '
        . ($active === $key
            ? 'bg-primary/10 text-primary font-medium'
            : 'hover:bg-base-200 text-base-content/80');
@endphp

{{-- ─── Desktop sidebar ─────────────────────────────────────────────────── --}}
<aside class="hidden lg:flex flex-col w-64 shrink-0 bg-base-100 border-r border-base-300 min-h-screen">

    {{-- Logo / institution --}}
    <div class="flex items-center gap-3 p-5 border-b border-base-300">
        <div class="size-9 rounded-lg border border-base-300 bg-base-200 p-1.5 shrink-0">
            <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" class="h-full w-full object-contain">
        </div>
        <span class="font-semibold truncate text-sm">{{ $institutionName }}</span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 p-3 space-y-1">

        {{-- Dashboard (all roles) --}}
        <a href="{{ route('dashboard') }}" class="{{ $navClass('dashboard') }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                <path d="M4 11.5 12 5l8 6.5V20a1 1 0 0 1-1 1h-4v-6H9v6H5a1 1 0 0 1-1-1v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Dashboard
        </a>

        {{-- Admin-only nav items --}}
        @if ($role === 'Admin')
            <a href="{{ route('admin.users.index') }}" class="{{ $navClass('users') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 4v6m3-3h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Manage Users
            </a>
            <a href="{{ route('admin.settings.edit') }}" class="{{ $navClass('settings') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
                </svg>
                Site Settings
            </a>
        @endif

        {{-- Teacher-only nav items --}}
        @if ($role === 'Teacher')
            <a href="{{ route('teacher.classes.index') }}" class="{{ $navClass('classes') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                My Classes
            </a>
            <a href="{{ route('teacher.excuses.index') }}" class="{{ $navClass('excuses') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14 2v6h6M16 13H8m8 4H8m2-8H8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Excuse Requests
            </a>
        @endif

        {{-- Student-only nav items --}}
        @if ($role === 'Student')
            <a href="{{ route('student.classes.index') }}" class="{{ $navClass('classes') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                My Classes
            </a>
            <a href="{{ route('student.attendance.index') }}" class="{{ $navClass('attendance') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m-6 9 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Attendance
            </a>
            <a href="{{ route('student.excuses.index') }}" class="{{ $navClass('excuses') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14 2v6h6M16 13H8m8 4H8m2-8H8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Excuses
            </a>
        @endif

    </nav>

    {{-- Student Scan QR — pinned to bottom of sidebar --}}
    @if ($role === 'Student')
        <div class="p-3 border-t border-base-300">
            <a href="{{ route('student.scan.index') }}" class="{{ $navClass('scan') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M3 7V5a2 2 0 0 1 2-2h2m10 0h2a2 2 0 0 1 2 2v2m0 10v2a2 2 0 0 1-2 2h-2M3 17v2a2 2 0 0 0 2 2h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 12h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Scan QR
            </a>
        </div>
    @endif

    {{-- User / profile / logout --}}
    <div class="p-3 border-t border-base-300">
        <a href="{{ route('profile.show', $user) }}" class="{{ $navClass('profile') }}">
            @if ($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="size-8 rounded-lg object-cover shrink-0">
            @else
                <span class="inline-flex items-center justify-center size-8 rounded-lg bg-primary/15 text-primary text-xs font-bold shrink-0">
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </span>
            @endif
            <div class="min-w-0">
                <p class="text-sm font-medium truncate">{{ $user->name }}</p>
                <p class="text-xs {{ $active === 'profile' ? 'text-primary/70' : 'text-base-content/60' }}">{{ $role }}</p>
            </div>
        </a>
        <button
            type="button"
            onclick="document.getElementById('logout-modal').showModal()"
            class="btn btn-ghost btn-sm w-full justify-start gap-2 rounded-xl text-base-content/70 normal-case font-normal mt-1"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Logout
        </button>
    </div>
</aside>

{{-- ─── Mobile top bar ─────────────────────────────────────────────────── --}}
<div class="lg:hidden fixed top-0 inset-x-0 z-30 flex items-center justify-between px-4 h-14 bg-base-100 border-b border-base-300">
    <div class="flex items-center gap-2">
        <button type="button" onclick="document.getElementById('mobile-drawer').classList.remove('translate-x-full'); document.getElementById('mobile-backdrop').classList.remove('hidden');" class="btn btn-ghost btn-sm btn-square rounded-lg" aria-label="Open menu">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true">
                <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
        <div class="size-7 rounded-md border border-base-300 bg-base-200 p-1 shrink-0">
            <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" class="h-full w-full object-contain">
        </div>
        <span class="font-semibold text-sm truncate max-w-36">{{ $institutionName }}</span>
    </div>
    <span class="badge badge-sm {{ $roleBadgeClass }}">{{ $role }}</span>
</div>

{{-- ─── Mobile drawer overlay ────────────────────────────────────────── --}}
<div id="mobile-backdrop" class="lg:hidden fixed inset-0 z-40 bg-black/40 hidden" onclick="document.getElementById('mobile-drawer').classList.add('translate-x-full'); this.classList.add('hidden');"></div>

{{-- ─── Mobile slide-out drawer (right side) ─────────────────────────── --}}
<aside id="mobile-drawer" class="lg:hidden fixed inset-y-0 right-0 z-50 w-72 bg-base-100 border-l border-base-300 translate-x-full transition-transform duration-200 ease-in-out flex flex-col">

    {{-- Drawer header --}}
    <div class="flex items-center justify-between p-4 border-b border-base-300">
        <div class="flex items-center gap-2">
            <div class="size-7 rounded-md border border-base-300 bg-base-200 p-1 shrink-0">
                <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" class="h-full w-full object-contain">
            </div>
            <span class="font-semibold text-sm truncate">{{ $institutionName }}</span>
        </div>
        <button type="button" onclick="document.getElementById('mobile-drawer').classList.add('translate-x-full'); document.getElementById('mobile-backdrop').classList.add('hidden');" class="btn btn-ghost btn-sm btn-square rounded-lg" aria-label="Close menu">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true">
                <path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>

    {{-- Navigation links --}}
    <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="{{ $navClass('dashboard') }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                <path d="M4 11.5 12 5l8 6.5V20a1 1 0 0 1-1 1h-4v-6H9v6H5a1 1 0 0 1-1-1v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Dashboard
        </a>

        @if ($role === 'Admin')
            <a href="{{ route('admin.users.index') }}" class="{{ $navClass('users') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 4v6m3-3h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Manage Users
            </a>
            <a href="{{ route('admin.settings.edit') }}" class="{{ $navClass('settings') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
                </svg>
                Site Settings
            </a>
        @endif

        @if ($role === 'Teacher')
            <a href="{{ route('teacher.classes.index') }}" class="{{ $navClass('classes') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                My Classes
            </a>
            <a href="{{ route('teacher.excuses.index') }}" class="{{ $navClass('excuses') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14 2v6h6M16 13H8m8 4H8m2-8H8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Excuse Requests
            </a>
        @endif

        @if ($role === 'Student')
            <a href="{{ route('student.classes.index') }}" class="{{ $navClass('classes') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                My Classes
            </a>
            <a href="{{ route('student.attendance.index') }}" class="{{ $navClass('attendance') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m-6 9 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Attendance
            </a>
            <a href="{{ route('student.excuses.index') }}" class="{{ $navClass('excuses') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14 2v6h6M16 13H8m8 4H8m2-8H8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Excuses
            </a>
            <a href="{{ route('student.scan.index') }}" class="{{ $navClass('scan') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M3 7V5a2 2 0 0 1 2-2h2m10 0h2a2 2 0 0 1 2 2v2m0 10v2a2 2 0 0 1-2 2h-2M3 17v2a2 2 0 0 0 2 2h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 12h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Scan QR
            </a>
        @endif
    </nav>

    {{-- User / profile / logout --}}
    <div class="p-3 border-t border-base-300">
        <a href="{{ route('profile.show', $user) }}" class="{{ $navClass('profile') }}">
            @if ($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="size-8 rounded-lg object-cover shrink-0">
            @else
                <span class="inline-flex items-center justify-center size-8 rounded-lg bg-primary/15 text-primary text-xs font-bold shrink-0">
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </span>
            @endif
            <div class="min-w-0">
                <p class="text-sm font-medium truncate">{{ $user->name }}</p>
                <p class="text-xs {{ $active === 'profile' ? 'text-primary/70' : 'text-base-content/60' }}">{{ $role }}</p>
            </div>
        </a>
        <button
            type="button"
            onclick="document.getElementById('logout-modal').showModal(); document.getElementById('mobile-drawer').classList.add('translate-x-full'); document.getElementById('mobile-backdrop').classList.add('hidden');"
            class="btn btn-ghost btn-sm w-full justify-start gap-2 rounded-xl text-base-content/70 normal-case font-normal mt-1"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Logout
        </button>
    </div>
</aside>

{{-- ─── Mobile floating Scan QR button (students only) ──────────────────── --}}
@if ($role === 'Student')
    <a href="{{ route('student.scan.index') }}" class="lg:hidden fixed bottom-6 right-6 z-30 btn btn-primary btn-circle size-14 shadow-xl" aria-label="Scan QR Code">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-6" aria-hidden="true">
            <path d="M3 7V5a2 2 0 0 1 2-2h2m10 0h2a2 2 0 0 1 2 2v2m0 10v2a2 2 0 0 1-2 2h-2M3 17v2a2 2 0 0 0 2 2h2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M7 12h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </a>
@endif

{{-- ─── Logout modal (rendered once per page via this component) ───────── --}}
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
