<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="valentine">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Attendify') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-base-200 text-base-content">
        @php
            $siteSettings = app(\App\Support\SiteSettings::class);
            $institutionName = $siteSettings->get('institution_name', 'Attendify');
            $institutionLogo = $siteSettings->get('institution_logo');
            $displayLogo = $institutionLogo ?: asset('assets/attendify.png');
        @endphp

        <main class="relative min-h-screen flex flex-col overflow-hidden">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0">
                <div class="absolute -top-16 -left-16 h-64 w-64 rounded-full bg-primary/20 blur-3xl"></div>
                <div class="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-secondary/20 blur-3xl"></div>
            </div>

            <section class="relative flex-1 grid lg:grid-cols-2 items-center gap-10 px-6 py-10 md:px-12 lg:px-20">
                <div class="hidden lg:flex items-center justify-center">
                    <div class="w-full max-w-md xl:max-w-lg p-10 transition hover:rotate-5">
                        <img
                            src="{{ $displayLogo }}"
                            alt="{{ $institutionName }} logo"
                            class="w-full h-full object-contain"
                        >
                    </div>
                </div>

                <div class="w-full max-w-2xl rounded-3xl bg-base-100/85 ring-1 ring-base-300/70 shadow-xl p-7 md:p-10 flex flex-col items-start gap-7 backdrop-blur-sm">
                    <span class="badge badge-primary badge-lg font-semibold">Built for modern institutions</span>

                    <h1 class="text-4xl md:text-6xl font-black leading-[1.04] tracking-tight">
                        <span class="block">{{ $institutionName }}</span>
                        <span class="block text-primary">Attendance System</span>
                    </h1>

                    <p class="text-base md:text-lg text-base-content/75 max-w-xl">
                        Smart, fast, and reliable attendance management for educational institutions.
                    </p>

                    <a href="{{ route('new.setup') }}" class="btn btn-primary btn-lg rounded-md px-8 shadow-lg shadow-primary/25 inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Setup System</span>
                    </a>
                </div>
            </section>

            <footer class="relative border-t border-base-300/70 px-6 py-5 md:px-12 lg:px-20 text-sm text-base-content/70 bg-base-100/70 backdrop-blur-sm">
                Attendify licensed under the MIT License. Copyright &copy; {{ now()->year }} Attendify Developers.
            </footer>
        </main>
    </body>
</html>
