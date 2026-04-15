@props(['title' => ''])

<x-layouts.app :title="$title">
    {{-- Fixed theme toggle --}}
    <button
        type="button"
        onclick="toggleTheme()"
        aria-label="Toggle theme"
        class="fixed top-4 right-4 z-50 inline-flex items-center justify-center size-9 rounded-xl bg-base-100/80 backdrop-blur-sm border border-base-300/40 shadow-sm text-base-content/60 hover:text-base-content hover:bg-base-100 transition-colors"
    >
        <svg class="theme-toggle-sun size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="1.8"/>
            <path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        <svg class="theme-toggle-moon size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <main class="relative min-h-screen grid place-items-center p-6 overflow-hidden">
        @if ($landingBanner)
            <img
                src="{{ $landingBanner }}"
                alt="Institution banner"
                class="absolute inset-0 h-full w-full object-cover opacity-15 blur-sm"
            >
        @endif

        {{-- Gradient overlay with brand colors --}}
        <div class="absolute inset-0 bg-gradient-to-br from-primary/10 via-base-200/90 to-secondary/10"></div>

        {{-- Decorative blobs --}}
        <div class="blob-a absolute top-1/4 -left-32 size-96 rounded-full bg-primary/8 blur-3xl pointer-events-none"></div>
        <div class="blob-b absolute bottom-1/4 -right-32 size-80 rounded-full bg-accent/8 blur-3xl pointer-events-none"></div>

        <section class="relative z-10 w-full max-w-md">
            <div class="af-glass rounded-2xl border border-base-300/40 shadow-2xl overflow-hidden">
                <div class="p-8 space-y-6">
                    {{-- Branding --}}
                    <div class="flex flex-col items-center gap-4 text-center">
                        <div class="size-16 rounded-2xl bg-base-200/80 border border-base-300/40 p-2.5 shadow-sm">
                            <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" class="h-full w-full object-contain">
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35">{{ $institutionName }}</p>
                            <h1 class="text-2xl font-black tracking-tight mt-1">{{ $title }}</h1>
                        </div>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </section>
    </main>
</x-layouts.app>
