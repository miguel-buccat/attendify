@props(['active' => null])

@php
    $user = auth()->user();
    $role = $user->role->value; // 'Admin', 'Teacher', 'Student'

    $rolePillVariant = match ($role) {
        'Admin'   => 'af-pill-primary',
        'Teacher' => 'af-pill-secondary',
        default   => 'af-pill-accent',
    };

    $navClass = fn (string $key) => 'sb-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 '
        . ($active === $key
            ? 'sb-active bg-primary/15 text-primary font-semibold'
            : 'sidebar-nav-link hover:bg-base-content/5 text-base-content/70 hover:text-base-content');
@endphp

{{-- ─── Desktop sidebar ─────────────────────────────────────────────────── --}}
<style>
    #desktop-sidebar { transition: width 200ms ease; }
    #desktop-sidebar.sb-c { width: 4.5rem; }
    #desktop-sidebar.sb-c .sb-text { display: none; }
    #desktop-sidebar.sb-c .sb-header { flex-direction: column; padding: 0.75rem; gap: 0.375rem; align-items: center; }
    #desktop-sidebar.sb-c .sb-header .sb-toggle { margin-left: 0; }
    #desktop-sidebar.sb-c .sb-toggle svg { transform: rotate(180deg); }
    #desktop-sidebar.sb-c .sb-link { font-size: 0; gap: 0; justify-content: center; padding-inline: 0; }
    #desktop-sidebar.sb-c .sb-link .badge, #desktop-sidebar.sb-c .sb-link .af-pill { display: none; }
    #desktop-sidebar.sb-c .sb-section { padding: 0.5rem; }
    #desktop-sidebar.sb-c .sb-actions { flex-direction: column-reverse; align-items: center; gap: 0.25rem; margin-top: 0.25rem; }
    #desktop-sidebar.sb-c .sb-actions > * { flex: none; width: 2.25rem; height: 2.25rem; min-height: unset; padding: 0; justify-content: center; gap: 0; font-size: 0; }
</style>
<aside id="desktop-sidebar" class="sidebar-panel hidden lg:flex flex-col w-64 shrink-0 bg-base-100 border-r border-base-300/50 sticky top-0 h-screen overflow-y-auto overflow-x-hidden af-scrollbar">

    {{-- Logo / institution --}}
    <div class="sb-header flex items-center gap-3 p-5 border-b af-divider">
        <div class="size-9 rounded-xl bg-gradient-to-br from-primary/20 to-secondary/20 p-1.5 shrink-0 ring-1 ring-primary/15">
            <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" class="h-full w-full object-contain">
        </div>
        <span class="sb-text font-bold truncate text-sm">{{ $institutionName }}</span>
        <button type="button" onclick="toggleSidebar()" class="sb-toggle ml-auto shrink-0 af-btn af-btn-ghost af-btn-icon af-btn-sm rounded-xl text-base-content/30 hover:text-base-content/60" aria-label="Toggle sidebar" title="Toggle sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 transition-transform duration-200"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 p-3 space-y-0.5">

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
    <div class="sb-section p-3 border-t af-divider">
        <a href="{{ route('notifications.index') }}" class="{{ $navClass('notification-center') }} relative">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9ZM13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Notifications
            <span id="sidebar-notif-badge" class="hidden ml-auto af-pill af-pill-primary text-[10px] px-1.5 py-0 min-w-[1.25rem] text-center"></span>
        </a>
        <button type="button" onclick="document.getElementById('about-modal').showModal()" class="sb-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 sidebar-nav-link hover:bg-base-content/5 text-base-content/70 hover:text-base-content w-full text-left">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/>
                <path d="M12 16v-4m0-4h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            About System
        </button>
    </div>

    {{-- Student Scan QR --}}
    @if ($role === 'Student')
        <div class="sb-section p-3 border-t af-divider">
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
    <div class="sb-section p-3 border-t af-divider">
        <button type="button" onclick="document.getElementById('profile-modal').showModal()" class="{{ $navClass('profile') }} w-full text-left">
            @if ($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="size-8 rounded-xl object-cover shrink-0 ring-1 ring-base-300/50">
            @else
                <span class="inline-flex items-center justify-center size-8 rounded-xl bg-primary/12 text-primary text-xs font-bold shrink-0">
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </span>
            @endif
            <div class="min-w-0 sb-text">
                <p class="text-sm font-semibold truncate">{{ $user->name }}</p>
                <p class="text-[11px] {{ $active === 'profile' ? 'text-primary/70' : 'text-base-content/45' }}">{{ $role }}</p>
            </div>
        </button>
        <div class="sb-actions flex items-center gap-1.5 mt-1.5">
            <button
                type="button"
                onclick="document.getElementById('logout-modal').showModal()"
                class="af-btn af-btn-ghost af-btn-sm flex-1 justify-start gap-2 text-base-content/60 font-normal"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Logout
            </button>
            <button type="button" onclick="toggleTheme()" class="af-btn af-btn-ghost af-btn-icon af-btn-sm text-base-content/40" aria-label="Toggle theme">
                <svg id="sidebar-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 hidden"><circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="1.8"/><path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <svg id="sidebar-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </div>
</aside>

{{-- ─── Mobile top bar ─────────────────────────────────────────────────── --}}
<div class="lg:hidden fixed top-0 inset-x-0 z-30 flex items-center justify-between px-4 h-14 af-glass border-b border-base-300/30">
    <div class="flex items-center gap-2.5">
        <div class="size-7 rounded-lg bg-gradient-to-br from-primary/20 to-secondary/20 p-1 shrink-0 ring-1 ring-primary/15">
            <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" class="h-full w-full object-contain">
        </div>
        <span class="font-bold text-sm truncate max-w-36">{{ $institutionName }}</span>
    </div>
    <button type="button" onclick="document.getElementById('mobile-drawer').classList.remove('translate-x-full'); document.getElementById('mobile-backdrop').classList.remove('hidden');" class="af-btn af-btn-ghost af-btn-icon af-btn-sm" aria-label="Open menu">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true">
            <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
    </button>
</div>

{{-- ─── Mobile drawer overlay ────────────────────────────────────────── --}}
<div id="mobile-backdrop" class="lg:hidden fixed inset-0 z-40 bg-black/50 backdrop-blur-sm hidden transition-opacity" onclick="document.getElementById('mobile-drawer').classList.add('translate-x-full'); this.classList.add('hidden');"></div>

{{-- ─── Mobile slide-out drawer (right side) ─────────────────────────── --}}
<aside id="mobile-drawer" class="sidebar-panel lg:hidden fixed inset-y-0 right-0 z-50 w-72 bg-base-100 border-l border-base-300/30 translate-x-full transition-transform duration-200 ease-in-out flex flex-col">

    {{-- Drawer header --}}
    <div class="flex items-center justify-between p-4 border-b af-divider">
        <div class="flex items-center gap-2.5">
            <div class="size-7 rounded-lg bg-gradient-to-br from-primary/20 to-secondary/20 p-1 shrink-0 ring-1 ring-primary/15">
                <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" class="h-full w-full object-contain">
            </div>
            <span class="font-bold text-sm truncate">{{ $institutionName }}</span>
        </div>
        <button type="button" onclick="document.getElementById('mobile-drawer').classList.add('translate-x-full'); document.getElementById('mobile-backdrop').classList.add('hidden');" class="af-btn af-btn-ghost af-btn-icon af-btn-sm" aria-label="Close menu">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true">
                <path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>

    {{-- Navigation links --}}
    <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto af-scrollbar">
        <a href="{{ route('dashboard') }}" class="{{ $navClass('dashboard') }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true">
                <path d="M4 11.5 12 5l8 6.5V20a1 1 0 0 1-1 1h-4v-6H9v6H5a1 1 0 0 1-1-1v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Dashboard
        </a>

        @if ($role === 'Admin')
            <a href="{{ route('admin.users.index') }}" class="{{ $navClass('users') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 4v6m3-3h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Manage Users
            </a>
            <a href="{{ route('admin.reports.index') }}" class="{{ $navClass('reports') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><path d="M21 12V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 22l2-2 4 4M16 3v4M8 3v4M3 11h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Reports
            </a>
            <a href="{{ route('admin.leaderboard.index') }}" class="{{ $navClass('leaderboard') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><path d="M8 21V12m4 9V8m4 13v-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Class Overview
            </a>
            <a href="{{ route('admin.activity-log.index') }}" class="{{ $navClass('activity-log') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Activity Log
            </a>
            <a href="{{ route('admin.settings.edit') }}" class="{{ $navClass('settings') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
                Site Settings
            </a>
        @endif

        @if ($role === 'Teacher')
            <a href="{{ route('teacher.classes.index') }}" class="{{ $navClass('classes') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                My Classes
            </a>
            <a href="{{ route('teacher.excuses.index') }}" class="{{ $navClass('excuses') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6M16 13H8m8 4H8m2-8H8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Excuse Requests
            </a>
        @endif

        @if ($role === 'Student')
            <a href="{{ route('student.classes.index') }}" class="{{ $navClass('classes') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                My Classes
            </a>
            <a href="{{ route('student.attendance.index') }}" class="{{ $navClass('attendance') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m-6 9 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Attendance
            </a>
            <a href="{{ route('student.excuses.index') }}" class="{{ $navClass('excuses') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6M16 13H8m8 4H8m2-8H8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Excuses
            </a>
            <a href="{{ route('student.calendar.index') }}" class="{{ $navClass('calendar') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                Calendar
            </a>
            <a href="{{ route('student.notifications.edit') }}" class="{{ $navClass('notification-prefs') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9ZM13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Notification Preferences
            </a>
            <a href="{{ route('student.scan.index') }}" class="{{ $navClass('scan') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><path d="M3 7V5a2 2 0 0 1 2-2h2m10 0h2a2 2 0 0 1 2 2v2m0 10v2a2 2 0 0 1-2 2h-2M3 17v2a2 2 0 0 0 2 2h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 12h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                Scan QR
            </a>
        @endif

        {{-- Notification center link (all roles) --}}
        <a href="{{ route('notifications.index') }}" class="{{ $navClass('notification-center') }} relative">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9ZM13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Notifications
            <span id="mobile-notif-badge" class="hidden ml-auto af-pill af-pill-primary text-[10px] px-1.5 py-0 min-w-[1.25rem] text-center"></span>
        </a>
        <button type="button" onclick="document.getElementById('about-modal').showModal()" class="sb-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 sidebar-nav-link hover:bg-base-content/5 text-base-content/70 hover:text-base-content w-full text-left">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/><path d="M12 16v-4m0-4h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            About System
        </button>
    </nav>

    {{-- User / profile / logout --}}
    <div class="p-3 border-t af-divider">
        <button type="button" onclick="document.getElementById('mobile-drawer').classList.add('translate-x-full'); document.getElementById('mobile-backdrop').classList.add('hidden'); document.getElementById('profile-modal').showModal()" class="{{ $navClass('profile') }} w-full text-left">
            @if ($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="size-8 rounded-xl object-cover shrink-0 ring-1 ring-base-300/50">
            @else
                <span class="inline-flex items-center justify-center size-8 rounded-xl bg-primary/12 text-primary text-xs font-bold shrink-0">
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </span>
            @endif
            <div class="min-w-0">
                <p class="text-sm font-semibold truncate">{{ $user->name }}</p>
                <p class="text-[11px] {{ $active === 'profile' ? 'text-primary/70' : 'text-base-content/45' }}">{{ $role }}</p>
            </div>
        </button>
        <div class="flex items-center gap-1.5 mt-1.5">
            <button
                type="button"
                onclick="document.getElementById('logout-modal').showModal(); document.getElementById('mobile-drawer').classList.add('translate-x-full'); document.getElementById('mobile-backdrop').classList.add('hidden');"
                class="af-btn af-btn-ghost af-btn-sm flex-1 justify-start gap-2 text-base-content/60 font-normal"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Logout
            </button>
            <button type="button" onclick="toggleTheme()" class="af-btn af-btn-ghost af-btn-icon af-btn-sm text-base-content/40" aria-label="Toggle theme">
                <svg id="mob-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 hidden"><circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="1.8"/><path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <svg id="mob-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </div>
</aside>

{{-- ─── Floating Scan QR button (students only) ──────────────────────────── --}}
@if ($role === 'Student')
    <a href="{{ route('student.scan.index') }}"
       class="lg:hidden fixed bottom-6 right-6 z-30
              flex items-center justify-center
              size-14 rounded-full
              bg-primary text-primary-content
              shadow-xl shadow-primary/25 hover:shadow-2xl hover:shadow-primary/40
              transition-all duration-200 hover:scale-105 active:scale-95"
       aria-label="Scan QR Code">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-6 shrink-0" aria-hidden="true">
            <path d="M3 7V5a2 2 0 0 1 2-2h2m10 0h2a2 2 0 0 1 2 2v2m0 10v2a2 2 0 0 1-2 2h-2M3 17v2a2 2 0 0 0 2 2h2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M7 12h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </a>
@endif

{{-- ─── Success toast ─── --}}
@if (session('success'))
<div id="success-toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[200] flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-success text-success-content text-sm font-bold shadow-xl shadow-success/25 pointer-events-none">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 shrink-0"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    {{ session('success') }}
</div>
<script>setTimeout(function(){var t=document.getElementById('success-toast');if(t){t.style.transition='opacity .4s';t.style.opacity='0';setTimeout(function(){t.remove()},400)}},3000);</script>
@endif

{{-- ─── Profile modal ───────────────────────────────────────────────────── --}}
<dialog id="profile-modal" class="modal">
    <div class="af-modal-box modal-box w-full max-w-md rounded-2xl p-0 overflow-hidden border border-base-300/30 shadow-2xl">

        {{-- ── View state ──────────────────────────────────── --}}
        <div id="profile-view-state">
            {{-- Banner --}}
            <div class="relative h-28 overflow-hidden">
                @if ($user->banner_url)
                    <img src="{{ $user->banner_url }}" class="absolute inset-0 h-full w-full object-cover">
                @else
                    <div class="h-full w-full bg-gradient-to-br from-primary/30 via-secondary/20 to-accent/25"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-base-100/80 to-transparent"></div>
                <form method="dialog" class="absolute top-3 right-3">
                    <button class="af-btn af-btn-ghost af-btn-icon af-btn-sm af-glass rounded-xl text-base-content/60 hover:text-base-content">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </form>
            </div>
            {{-- Avatar + info --}}
            <div class="relative z-10 px-6 -mt-10 pb-6">
                <div class="mb-4">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" class="size-18 rounded-2xl object-cover border-4 border-base-100 shadow-lg">
                    @else
                        <span class="inline-flex items-center justify-center size-18 rounded-2xl bg-primary/12 text-primary text-2xl font-black border-4 border-base-100 shadow-lg">
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
                                    'Admin'   => 'primary',
                                    'Teacher' => 'secondary',
                                    default   => 'accent',
                                };
                            @endphp
                            <x-ui.badge :variant="$modalRolePill">{{ $role }}</x-ui.badge>
                        </div>
                        <p class="text-sm text-base-content/60 mt-0.5">{{ $user->email }}</p>
                        <p class="text-xs text-base-content/40 mt-1">Member since {{ $user->created_at->format('F Y') }}</p>
                    </div>
                    <button
                        type="button"
                        onclick="showProfileEdit()"
                        class="af-btn af-btn-outline af-btn-sm shrink-0 mt-1"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Edit
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Edit state ───────────────────────────────────── --}}
        <div id="profile-edit-state" class="hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b af-divider">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="showProfileView()" class="af-btn af-btn-ghost af-btn-icon af-btn-sm text-base-content/40">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <h3 class="font-black text-base tracking-tight">Edit Profile</h3>
                </div>
                <form method="dialog">
                    <button class="af-btn af-btn-ghost af-btn-icon af-btn-sm text-base-content/40">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                </form>
            </div>

            @if ($errors->hasAny(['avatar', 'banner']))
                <div class="mx-5 mt-4">
                    <x-ui.alert variant="error" :dismissible="false">
                        @foreach ($errors->only(['avatar', 'banner']) as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </x-ui.alert>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="px-5 py-5 space-y-4">
                @csrf
                @method('PATCH')

                {{-- Avatar --}}
                <div class="af-card overflow-hidden">
                    <div class="px-4 py-3 border-b af-divider">
                        <h4 class="font-semibold text-xs">Avatar</h4>
                    </div>
                    <div class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="shrink-0">
                                @if ($user->avatar_url)
                                    <img id="modal-avatar-preview" src="{{ $user->avatar_url }}" class="size-12 rounded-xl object-cover ring-1 ring-base-300/40">
                                @else
                                    <span id="modal-avatar-placeholder" class="inline-flex items-center justify-center size-12 rounded-xl bg-primary/12 text-primary text-sm font-bold ring-1 ring-base-300/40">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                    <img id="modal-avatar-preview" src="" class="size-12 rounded-xl object-cover ring-1 ring-base-300/40 hidden">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp"
                                    class="af-input text-xs file:mr-2 file:border-0 file:bg-primary/10 file:text-primary file:text-xs file:font-semibold file:px-2 file:py-1 file:rounded-lg {{ $errors->has('avatar') ? 'af-input-error' : '' }}"
                                    onchange="previewModalImage(this, 'modal-avatar-preview', 'modal-avatar-placeholder')">
                                <p class="text-xs text-base-content/40 mt-1">JPG, PNG or WebP · max 2 MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Banner --}}
                <div class="af-card overflow-hidden">
                    <div class="px-4 py-3 border-b af-divider">
                        <h4 class="font-semibold text-xs">Banner Image</h4>
                    </div>
                    <div class="px-4 py-3 space-y-2">
                        @if ($user->banner_url)
                            <img id="modal-banner-preview" src="{{ $user->banner_url }}" class="h-16 w-full rounded-xl object-cover ring-1 ring-base-300/40">
                        @else
                            <div id="modal-banner-placeholder" class="h-16 w-full rounded-xl bg-gradient-to-br from-primary/20 via-secondary/10 to-accent/20 ring-1 ring-base-300/40"></div>
                            <img id="modal-banner-preview" src="" class="h-16 w-full rounded-xl object-cover ring-1 ring-base-300/40 hidden">
                        @endif
                        <input type="file" name="banner" accept="image/jpeg,image/png,image/webp"
                            class="af-input text-xs file:mr-2 file:border-0 file:bg-primary/10 file:text-primary file:text-xs file:font-semibold file:px-2 file:py-1 file:rounded-lg {{ $errors->has('banner') ? 'af-input-error' : '' }}"
                            onchange="previewModalImage(this, 'modal-banner-preview', 'modal-banner-placeholder')">
                        <p class="text-xs text-base-content/40">JPG, PNG or WebP · max 4 MB</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <x-ui.button type="button" variant="ghost" size="sm" onclick="showProfileView()">Cancel</x-ui.button>
                    <x-ui.button type="submit" variant="primary" size="sm">Save Changes</x-ui.button>
                </div>
            </form>
        </div>

    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

{{-- ─── Logout modal ───────── --}}
<dialog id="logout-modal" class="modal">
    <div class="af-modal-box modal-box rounded-2xl border border-base-300/30 shadow-2xl">
        <h3 class="text-lg font-bold">Confirm Logout</h3>
        <p class="mt-2 text-base-content/60 text-sm">Are you sure you want to log out?</p>
        <div class="modal-action">
            <form method="dialog">
                <x-ui.button variant="ghost">Cancel</x-ui.button>
            </form>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-ui.button type="submit" variant="danger">Logout</x-ui.button>
            </form>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

{{-- ─── About modal ─────────── --}}
<dialog id="about-modal" class="modal">
    <div class="af-modal-box modal-box rounded-2xl border border-base-300/30 shadow-2xl max-w-sm text-center">
        <form method="dialog" class="absolute top-3 right-3">
            <button class="af-btn af-btn-ghost af-btn-icon af-btn-sm rounded-xl text-base-content/60 hover:text-base-content">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
        </form>
        <div class="flex flex-col items-center gap-4 py-2">
            <div class="size-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 p-2.5 ring-1 ring-primary/15">
                <img src="{{ asset('assets/attendify.png') }}" alt="Attendify" class="h-full w-full object-contain">
            </div>
            <div>
                <h3 class="text-xl font-black tracking-tight">Attendify</h3>
                <p class="text-sm text-base-content/60 mt-2 leading-relaxed">A modern attendance management system for educational institutions. Track sessions, manage classes, and monitor student attendance with ease.</p>
            </div>
            <a href="/assets/Attendify_User_Manual.pdf" target="_blank" rel="noopener noreferrer" class="af-btn af-btn-outline af-btn-sm gap-2 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6M16 13H8m8 4H8m2-8H8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                View User Manual
            </a>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
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
