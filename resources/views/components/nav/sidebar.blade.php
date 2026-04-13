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
    $navClass = fn (string $key) => 'sb-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors '
        . ($active === $key
            ? 'bg-primary/10 text-primary font-medium'
            : 'sidebar-nav-link hover:bg-base-200 text-base-content/80');
@endphp

{{-- ─── Desktop sidebar ─────────────────────────────────────────────────── --}}
<style>
    #desktop-sidebar { transition: width 200ms ease; }
    #desktop-sidebar.sb-c { width: 4.5rem; }
    #desktop-sidebar.sb-c .sb-text { display: none; }
    /* Header: stack logo + toggle vertically so toggle stays reachable */
    #desktop-sidebar.sb-c .sb-header { flex-direction: column; padding: 0.75rem; gap: 0.375rem; align-items: center; }
    #desktop-sidebar.sb-c .sb-header .sb-toggle { margin-left: 0; }
    #desktop-sidebar.sb-c .sb-toggle svg { transform: rotate(180deg); }
    /* Nav links: icon-only centered */
    #desktop-sidebar.sb-c .sb-link { font-size: 0; gap: 0; justify-content: center; padding-inline: 0; }
    #desktop-sidebar.sb-c .sb-link .badge { display: none; }
    /* Bottom sections: tighter padding */
    #desktop-sidebar.sb-c .sb-section { padding: 0.5rem; }
    /* Logout + theme: stack vertically as centered icon squares, theme on top */
    #desktop-sidebar.sb-c .sb-actions { flex-direction: column-reverse; align-items: center; gap: 0.25rem; margin-top: 0.25rem; }
    #desktop-sidebar.sb-c .sb-actions > * { flex: none; width: 2.25rem; height: 2.25rem; min-height: unset; padding: 0; justify-content: center; gap: 0; font-size: 0; }
</style>
<aside id="desktop-sidebar" class="sidebar-panel hidden lg:flex flex-col w-64 shrink-0 bg-base-100 border-r border-base-300 sticky top-0 h-screen overflow-y-auto overflow-x-hidden">

    {{-- Logo / institution --}}
    <div class="sb-header flex items-center gap-3 p-5 border-b border-base-300">
        <div class="size-9 rounded-lg border border-base-300 bg-base-200 p-1.5 shrink-0">
            <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" class="h-full w-full object-contain">
        </div>
        <span class="sb-text font-semibold truncate text-sm">{{ $institutionName }}</span>
        <button type="button" onclick="toggleSidebar()" class="sb-toggle ml-auto shrink-0 inline-flex items-center justify-center size-7 rounded-lg text-base-content/30 hover:text-base-content/60 hover:bg-base-200 transition-colors" aria-label="Toggle sidebar" title="Toggle sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 transition-transform duration-200"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
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
            <a href="{{ route('admin.reports.index') }}" class="{{ $navClass('reports') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M21 12V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 22l2-2 4 4M16 3v4M8 3v4M3 11h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Reports
            </a>
            <a href="{{ route('admin.leaderboard.index') }}" class="{{ $navClass('leaderboard') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M8 21V12m4 9V8m4 13v-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Class Overview
            </a>
            <a href="{{ route('admin.activity-log.index') }}" class="{{ $navClass('activity-log') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Activity Log
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
            <a href="{{ route('student.calendar.index') }}" class="{{ $navClass('calendar') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Calendar
            </a>
            <a href="{{ route('student.notifications.edit') }}" class="{{ $navClass('notification-prefs') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9ZM13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Notification Preferences
            </a>
        @endif

    </nav>

    {{-- Notification bell --}}
    <div class="sb-section p-3 border-t border-base-300">
        <a href="{{ route('notifications.index') }}" class="{{ $navClass('notification-center') }} relative">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9ZM13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Notifications
            <span id="sidebar-notif-badge" class="hidden ml-auto badge badge-primary badge-xs rounded-full"></span>
        </a>
    </div>

    {{-- Student Scan QR — pinned to bottom of sidebar --}}
    @if ($role === 'Student')
        <div class="sb-section p-3 border-t border-base-300">
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
    <div class="sb-section p-3 border-t border-base-300">
        <button type="button" onclick="document.getElementById('profile-modal').showModal()" class="{{ $navClass('profile') }} w-full text-left">
            @if ($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="size-8 rounded-lg object-cover shrink-0">
            @else
                <span class="inline-flex items-center justify-center size-8 rounded-lg bg-primary/15 text-primary text-xs font-bold shrink-0">
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </span>
            @endif
            <div class="min-w-0 sb-text">
                <p class="text-sm font-medium truncate">{{ $user->name }}</p>
                <p class="text-xs {{ $active === 'profile' ? 'text-primary/70' : 'text-base-content/60' }}">{{ $role }}</p>
            </div>
        </button>
        <div class="sb-actions flex items-center gap-1.5 mt-1">
            <button
                type="button"
                onclick="document.getElementById('logout-modal').showModal()"
                class="btn btn-ghost btn-sm flex-1 justify-start gap-2 rounded-xl text-base-content/70 normal-case font-normal"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Logout
            </button>
            <button type="button" onclick="toggleTheme()" class="btn btn-ghost btn-sm btn-square rounded-xl text-base-content/50" aria-label="Toggle theme">
                <svg id="sidebar-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 hidden"><circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="1.8"/><path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <svg id="sidebar-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
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
    <button type="button" onclick="document.getElementById('mobile-drawer').classList.remove('translate-x-full'); document.getElementById('mobile-backdrop').classList.remove('hidden');" class="btn btn-ghost btn-sm btn-square rounded-lg" aria-label="Open menu">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true">
                <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
</div>

{{-- ─── Mobile drawer overlay ────────────────────────────────────────── --}}
<div id="mobile-backdrop" class="lg:hidden fixed inset-0 z-40 bg-black/40 hidden" onclick="document.getElementById('mobile-drawer').classList.add('translate-x-full'); this.classList.add('hidden');"></div>

{{-- ─── Mobile slide-out drawer (right side) ─────────────────────────── --}}
<aside id="mobile-drawer" class="sidebar-panel lg:hidden fixed inset-y-0 right-0 z-50 w-72 bg-base-100 border-l border-base-300 translate-x-full transition-transform duration-200 ease-in-out flex flex-col">

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
            <a href="{{ route('admin.reports.index') }}" class="{{ $navClass('reports') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M21 12V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 22l2-2 4 4M16 3v4M8 3v4M3 11h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Reports
            </a>
            <a href="{{ route('admin.leaderboard.index') }}" class="{{ $navClass('leaderboard') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M8 21V12m4 9V8m4 13v-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Class Overview
            </a>
            <a href="{{ route('admin.activity-log.index') }}" class="{{ $navClass('activity-log') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Activity Log
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
            <a href="{{ route('student.calendar.index') }}" class="{{ $navClass('calendar') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Calendar
            </a>
            <a href="{{ route('student.notifications.edit') }}" class="{{ $navClass('notification-prefs') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9ZM13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Notification Preferences
            </a>
            <a href="{{ route('student.scan.index') }}" class="{{ $navClass('scan') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                    <path d="M3 7V5a2 2 0 0 1 2-2h2m10 0h2a2 2 0 0 1 2 2v2m0 10v2a2 2 0 0 1-2 2h-2M3 17v2a2 2 0 0 0 2 2h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 12h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Scan QR
            </a>
        @endif

        {{-- Notification center link (all roles) --}}
        <a href="{{ route('notifications.index') }}" class="{{ $navClass('notification-center') }} relative">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9ZM13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Notifications
            <span id="mobile-notif-badge" class="hidden ml-auto badge badge-primary badge-xs rounded-full"></span>
        </a>
    </nav>

    {{-- User / profile / logout --}}
    <div class="p-3 border-t border-base-300">
        <button type="button" onclick="document.getElementById('mobile-drawer').classList.add('translate-x-full'); document.getElementById('mobile-backdrop').classList.add('hidden'); document.getElementById('profile-modal').showModal()" class="{{ $navClass('profile') }} w-full text-left">
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
        </button>
        <div class="flex items-center gap-1.5 mt-1">
            <button
                type="button"
                onclick="document.getElementById('logout-modal').showModal(); document.getElementById('mobile-drawer').classList.add('translate-x-full'); document.getElementById('mobile-backdrop').classList.add('hidden');"
                class="btn btn-ghost btn-sm flex-1 justify-start gap-2 rounded-xl text-base-content/70 normal-case font-normal"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Logout
            </button>
            <button type="button" onclick="toggleTheme()" class="btn btn-ghost btn-sm btn-square rounded-xl text-base-content/50" aria-label="Toggle theme">
                <svg id="mob-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 hidden"><circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="1.8"/><path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <svg id="mob-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
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

{{-- ─── Success toast (shows on any page after a successful profile update) ─── --}}
@if (session('success'))
<div id="success-toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[200] flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-success text-success-content text-sm font-semibold shadow-xl shadow-success/25 pointer-events-none">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 shrink-0"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    {{ session('success') }}
</div>
<script>setTimeout(function(){var t=document.getElementById('success-toast');if(t){t.style.transition='opacity .4s';t.style.opacity='0';setTimeout(function(){t.remove()},400)}},3000);</script>
@endif

{{-- ─── Profile modal ───────────────────────────────────────────────────── --}}
<dialog id="profile-modal" class="modal">
    <div class="modal-box w-full max-w-md rounded-3xl p-0 overflow-hidden">

        {{-- ── View state ──────────────────────────────────── --}}
        <div id="profile-view-state">
            {{-- Banner --}}
            <div class="relative h-32 overflow-hidden">
                @if ($user->banner_url)
                    <img src="{{ $user->banner_url }}" class="absolute inset-0 h-full w-full object-cover">
                @else
                    <div class="h-full w-full bg-gradient-to-br from-primary/40 via-secondary/20 to-accent/30"></div>
                @endif
                <form method="dialog" class="absolute top-3 right-3">
                    <button class="inline-flex items-center justify-center size-8 rounded-xl bg-base-100/80 backdrop-blur text-base-content/70 hover:bg-base-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </form>
            </div>
            {{-- Avatar + info --}}
            <div class="relative z-10 px-6 -mt-10 pb-6">
                <div class="mb-4">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" class="size-20 rounded-2xl object-cover border-4 border-base-100 shadow-lg">
                    @else
                        <span class="inline-flex items-center justify-center size-20 rounded-2xl bg-primary/15 text-primary text-2xl font-black border-4 border-base-100 shadow-lg">
                            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                        </span>
                    @endif
                </div>
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-xl font-black tracking-tight">{{ $user->name }}</h3>
                            @php
                                $modalRolePill = match ($role) {
                                    'Admin'   => 'text-primary bg-primary/10 border-primary/20',
                                    'Teacher' => 'text-secondary bg-secondary/10 border-secondary/20',
                                    default   => 'text-accent bg-accent/10 border-accent/20',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $modalRolePill }}">{{ $role }}</span>
                        </div>
                        <p class="text-sm text-base-content/65 mt-0.5">{{ $user->email }}</p>
                        <p class="text-xs text-base-content/45 mt-1">Member since {{ $user->created_at->format('F Y') }}</p>
                    </div>
                    <button
                        type="button"
                        onclick="showProfileEdit()"
                        class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-base-200 text-base-content/70 border border-base-300/50 text-xs font-medium hover:bg-base-300/50 transition-colors mt-1"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Edit
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Edit state ───────────────────────────────────── --}}
        <div id="profile-edit-state" class="hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-base-300/40">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="showProfileView()" class="btn btn-ghost btn-sm btn-square rounded-xl text-base-content/50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <h3 class="font-black text-base tracking-tight">Edit Profile</h3>
                </div>
                <form method="dialog">
                    <button class="btn btn-ghost btn-sm btn-square rounded-xl text-base-content/50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                </form>
            </div>

            @if ($errors->hasAny(['avatar', 'banner']))
                <div class="mx-5 mt-4 rounded-2xl border border-error/30 bg-error/5 px-4 py-3 space-y-1">
                    @foreach ($errors->only(['avatar', 'banner']) as $error)
                        <p class="text-sm text-error">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="px-5 py-5 space-y-4">
                @csrf
                @method('PATCH')

                {{-- Avatar --}}
                <div class="rounded-xl border border-base-300/50 bg-base-200/30 overflow-hidden">
                    <div class="px-4 py-3 border-b border-base-300/30">
                        <h4 class="font-semibold text-xs">Avatar</h4>
                    </div>
                    <div class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="shrink-0">
                                @if ($user->avatar_url)
                                    <img id="modal-avatar-preview" src="{{ $user->avatar_url }}" class="size-12 rounded-xl object-cover border border-base-300/50">
                                @else
                                    <span id="modal-avatar-placeholder" class="inline-flex items-center justify-center size-12 rounded-xl bg-primary/15 text-primary text-sm font-bold border border-base-300/50">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                    <img id="modal-avatar-preview" src="" class="size-12 rounded-xl object-cover border border-base-300/50 hidden">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp"
                                    class="w-full rounded-xl border border-base-300/70 bg-base-100 px-3 py-1.5 text-xs file:mr-2 file:border-0 file:bg-primary/10 file:text-primary file:text-xs file:font-semibold file:px-2 file:py-1 file:rounded-lg focus:outline-none {{ $errors->has('avatar') ? 'border-error' : '' }}"
                                    onchange="previewModalImage(this, 'modal-avatar-preview', 'modal-avatar-placeholder')">
                                <p class="text-xs text-base-content/45 mt-1">JPG, PNG or WebP · max 2 MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Banner --}}
                <div class="rounded-xl border border-base-300/50 bg-base-200/30 overflow-hidden">
                    <div class="px-4 py-3 border-b border-base-300/30">
                        <h4 class="font-semibold text-xs">Banner Image</h4>
                    </div>
                    <div class="px-4 py-3 space-y-2">
                        @if ($user->banner_url)
                            <img id="modal-banner-preview" src="{{ $user->banner_url }}" class="h-16 w-full rounded-xl object-cover border border-base-300/50">
                        @else
                            <div id="modal-banner-placeholder" class="h-16 w-full rounded-xl bg-gradient-to-br from-primary/20 via-secondary/10 to-accent/20 border border-base-300/50"></div>
                            <img id="modal-banner-preview" src="" class="h-16 w-full rounded-xl object-cover border border-base-300/50 hidden">
                        @endif
                        <input type="file" name="banner" accept="image/jpeg,image/png,image/webp"
                            class="w-full rounded-xl border border-base-300/70 bg-base-100 px-3 py-1.5 text-xs file:mr-2 file:border-0 file:bg-primary/10 file:text-primary file:text-xs file:font-semibold file:px-2 file:py-1 file:rounded-lg focus:outline-none {{ $errors->has('banner') ? 'border-error' : '' }}"
                            onchange="previewModalImage(this, 'modal-banner-preview', 'modal-banner-placeholder')">
                        <p class="text-xs text-base-content/45">JPG, PNG or WebP · max 4 MB</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <button type="button" onclick="showProfileView()" class="inline-flex items-center px-3 py-2 rounded-xl bg-base-200 text-base-content/60 border border-base-300/50 text-xs font-medium hover:bg-base-300/50 transition-colors">Cancel</button>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-primary-content text-xs font-semibold hover:opacity-90 transition-opacity">Save Changes</button>
                </div>
            </form>
        </div>

    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

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

<script>
function showProfileEdit() {
    document.getElementById('profile-view-state').classList.add('hidden');
    document.getElementById('profile-edit-state').classList.remove('hidden');
}
function showProfileView() {
    document.getElementById('profile-edit-state').classList.add('hidden');
    document.getElementById('profile-view-state').classList.remove('hidden');
}
function previewModalImage(input, previewId, placeholderId) {
    var preview = document.getElementById(previewId);
    var placeholder = placeholderId ? document.getElementById(placeholderId) : null;
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) { placeholder.classList.add('hidden'); }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
@if ($errors->hasAny(['avatar', 'banner']))
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('profile-modal');
    if (modal) { modal.showModal(); showProfileEdit(); }
});
@endif
function toggleTheme() {
    var current = document.documentElement.getAttribute('data-theme');
    var next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    syncThemeIcons(next);
}
function syncThemeIcons(theme) {
    var isDark = theme === 'dark';
    ['sidebar-sun','mob-sun'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.classList.toggle('hidden', !isDark);
    });
    ['sidebar-moon','mob-moon'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.classList.toggle('hidden', isDark);
    });
}
syncThemeIcons(document.documentElement.getAttribute('data-theme') || 'light');

// ── Collapsible sidebar ───────────────────────────────────────────────
function toggleSidebar() {
    var sb = document.getElementById('desktop-sidebar');
    if (!sb) return;
    var collapsed = sb.classList.toggle('sb-c');
    localStorage.setItem('sidebar-collapsed', collapsed ? '1' : '0');
}
(function() {
    if (localStorage.getItem('sidebar-collapsed') === '1') {
        var sb = document.getElementById('desktop-sidebar');
        if (sb) sb.classList.add('sb-c');
    }
})();
</script>
