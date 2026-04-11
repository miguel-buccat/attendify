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
        @endif

        {{-- Teacher-only nav items --}}
        @if ($role === 'Teacher')
            <a href="{{ route('teacher.classes.index') }}" class="{{ $navClass('classes') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                My Classes
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
        <div class="size-7 rounded-md border border-base-300 bg-base-200 p-1 shrink-0">
            <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" class="h-full w-full object-contain">
        </div>
        <span class="font-semibold text-sm truncate max-w-36">{{ $institutionName }}</span>
    </div>
    <div class="flex items-center gap-2">
        <span class="badge badge-sm {{ $roleBadgeClass }}">{{ $role }}</span>
        <button type="button" onclick="document.getElementById('logout-modal').showModal()" class="btn btn-ghost btn-xs rounded-lg">Logout</button>
    </div>
</div>

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
