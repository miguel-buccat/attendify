<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="valentine">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Dashboard - {{ config('app.name', 'Attendify') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-base-200 text-base-content">
        @php
            $siteSettings = app(\App\Support\SiteSettings::class);
            $institutionName = $siteSettings->get('institution_name', 'Attendify');
            $institutionLogo = $siteSettings->get('institution_logo') ?: asset('assets/attendify.png');
            $landingBanner = $siteSettings->get('landing_banner');
            $user = auth()->user();
        @endphp

        <div class="relative min-h-screen flex flex-col overflow-hidden">
            @if ($landingBanner)
                <img
                    src="{{ $landingBanner }}"
                    alt="Institution banner"
                    class="absolute inset-0 h-full w-full object-cover opacity-15"
                >
            @endif

            <div class="absolute inset-0 bg-gradient-to-b from-base-300/60 via-base-200/85 to-base-100/90"></div>

            <header class="relative z-10 px-4 py-4 md:px-8 md:py-6">
                <nav class="rounded-2xl border border-base-300/70 bg-base-100/90 backdrop-blur-md shadow-lg">
                    <div class="flex items-center justify-between gap-4 border-b border-base-300/60 px-4 py-4 md:px-6">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="size-11 rounded-xl border border-base-300 bg-base-200 p-2">
                                <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" class="h-full w-full object-contain">
                            </div>
                            <div class="min-w-0">
                                <h1 class="truncate text-lg md:text-xl font-semibold">{{ $institutionName }}</h1>
                            </div>
                        </div>

                        <div class="dropdown dropdown-end">
                            <button tabindex="0" class="btn btn-ghost btn-sm md:btn-md rounded-xl gap-2 normal-case">
                                <span class="inline-flex items-center justify-center size-8 rounded-lg bg-primary/15 text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true">
                                        <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="hidden sm:inline">{{ $user->name }}</span>
                            </button>

                            <ul tabindex="0" class="dropdown-content menu w-56 rounded-xl border border-base-300 bg-base-100 p-2 shadow-xl">
                                <li class="menu-title">
                                    <span>{{ $user->name }}</span>
                                    <span class="text-xs text-base-content/60">Role: {{ $user->role?->value ?? $user->role }}</span>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="px-3 py-3 md:px-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-2 justify-items-center">
                            <button class="btn btn-primary btn-sm md:btn-md rounded-xl w-full max-w-36 gap-2 normal-case">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 md:size-5" aria-hidden="true">
                                    <path d="M4 11.5 12 5l8 6.5V20a1 1 0 0 1-1 1h-4v-6H9v6H5a1 1 0 0 1-1-1v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>Dashboard</span>
                            </button>

                            <button class="btn btn-ghost btn-sm md:btn-md rounded-xl w-full max-w-36 gap-2 normal-case">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 md:size-5" aria-hidden="true">
                                    <path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                </svg>
                                <span>Menu 1</span>
                            </button>

                            <button class="btn btn-ghost btn-sm md:btn-md rounded-xl w-full max-w-36 gap-2 normal-case">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 md:size-5" aria-hidden="true">
                                    <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8" />
                                </svg>
                                <span>Menu 2</span>
                            </button>

                            <button class="btn btn-ghost btn-sm md:btn-md rounded-xl w-full max-w-36 gap-2 normal-case">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 md:size-5" aria-hidden="true">
                                    <path d="M5 8h14v10H5zM8 8V6h8v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>Menu 3</span>
                            </button>

                            <button class="btn btn-ghost btn-sm md:btn-md rounded-xl w-full max-w-36 gap-2 normal-case">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 md:size-5" aria-hidden="true">
                                    <path d="M7 12h10M12 7v10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                </svg>
                                <span>Menu 4</span>
                            </button>

                            <button class="btn btn-ghost btn-sm md:btn-md rounded-xl w-full max-w-36 gap-2 normal-case">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 md:size-5" aria-hidden="true">
                                    <path d="M6 12h12M12 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>Menu 5</span>
                            </button>

                            <button class="btn btn-ghost btn-sm md:btn-md rounded-xl w-full max-w-36 gap-2 normal-case">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 md:size-5" aria-hidden="true">
                                    <path d="M8 7h8v10H8z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                </svg>
                                <span>Menu 6</span>
                            </button>

                            <button class="btn btn-ghost btn-sm md:btn-md rounded-xl w-full max-w-36 gap-2 normal-case">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 md:size-5" aria-hidden="true">
                                    <path d="M12 4v16M4 12h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                </svg>
                                <span>Menu 7</span>
                            </button>
                        </div>
                    </div>
                </nav>
            </header>

            <main class="relative z-10 flex-1 px-4 pb-6 md:px-8 md:pb-8">
                <section class="rounded-2xl border border-base-300/70 bg-base-100/85 backdrop-blur-sm shadow p-6 md:p-8 space-y-6">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-semibold">Hello, {{ $user->name }}</h2>
                        <p class="mt-2 text-base-content/70">Role: {{ $user->role?->value ?? $user->role }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                        <article class="rounded-xl border border-base-300 bg-base-100 p-4">
                            <p class="text-xs uppercase tracking-wider text-base-content/60">Total Students</p>
                            <p class="mt-2 text-3xl font-bold">0</p>
                        </article>
                        <article class="rounded-xl border border-base-300 bg-base-100 p-4">
                            <p class="text-xs uppercase tracking-wider text-base-content/60">Total Teachers</p>
                            <p class="mt-2 text-3xl font-bold">0</p>
                        </article>
                        <article class="rounded-xl border border-base-300 bg-base-100 p-4">
                            <p class="text-xs uppercase tracking-wider text-base-content/60">Classes Today</p>
                            <p class="mt-2 text-3xl font-bold">0</p>
                        </article>
                        <article class="rounded-xl border border-base-300 bg-base-100 p-4">
                            <p class="text-xs uppercase tracking-wider text-base-content/60">Attendance Rate</p>
                            <p class="mt-2 text-3xl font-bold">0%</p>
                        </article>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                        <article class="xl:col-span-2 rounded-xl border border-base-300 bg-base-100 p-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold">Attendance Trend</h3>
                                <span class="badge badge-ghost">Placeholder</span>
                            </div>
                            <div class="mt-4 h-56 rounded-lg border border-dashed border-base-300 bg-base-200/70 grid place-items-center text-sm text-base-content/60">
                                Line Chart Placeholder
                            </div>
                        </article>

                        <article class="rounded-xl border border-base-300 bg-base-100 p-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold">Role Distribution</h3>
                                <span class="badge badge-ghost">Placeholder</span>
                            </div>
                            <div class="mt-4 h-56 rounded-lg border border-dashed border-base-300 bg-base-200/70 grid place-items-center text-sm text-base-content/60">
                                Pie Chart Placeholder
                            </div>
                        </article>
                    </div>
                </section>
            </main>

            <footer class="relative z-10 mt-auto px-4 pb-4 md:px-8 md:pb-6">
                <div class="rounded-2xl border border-base-300/70 bg-base-100/90 backdrop-blur-sm px-4 py-4 md:px-6 md:py-5 flex flex-col md:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/attendify.png') }}" alt="Attendify logo" class="size-8 object-contain">
                        <p class="text-sm text-base-content/70">Copyright &copy; {{ now()->year }} Attendify</p>
                    </div>

                    <div class="flex items-center gap-4 text-sm text-base-content/70">
                        <span>Licensed under MIT</span>
                        <a href="https://github.com" target="_blank" rel="noreferrer" class="link link-hover">GitHub</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
