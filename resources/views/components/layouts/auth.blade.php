@props(['title' => ''])

<x-layouts.app :title="$title">
    <main class="relative min-h-screen grid place-items-center p-6 overflow-hidden">
        @if ($landingBanner)
            <img
                src="{{ $landingBanner }}"
                alt="Institution banner"
                class="absolute inset-0 h-full w-full object-cover opacity-20"
            >
        @endif

        <div class="absolute inset-0 bg-gradient-to-br from-base-300/70 via-base-200/80 to-base-100/80"></div>

        <section class="card relative z-10 w-full max-w-md bg-base-100/95 backdrop-blur-sm shadow-2xl border border-base-300/80">
            <div class="card-body gap-5">
                <div class="flex flex-col items-center gap-3 text-center">
                    <div class="size-20 rounded-2xl bg-base-200 border border-base-300 p-3 shadow-sm">
                        <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" class="h-full w-full object-contain">
                    </div>
                    <div>
                        <p class="text-sm uppercase tracking-wider text-base-content/60">{{ $institutionName }}</p>
                        <h1 class="text-2xl font-semibold">{{ $title }}</h1>
                    </div>
                </div>

                {{ $slot }}
            </div>
        </section>
    </main>
</x-layouts.app>
