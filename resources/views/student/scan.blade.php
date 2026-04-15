<x-layouts.app title="Scan QR Code">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="scan" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6 max-w-lg mx-auto">

                <div class="d d1">
                    <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Student</p>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight">Scan Attendance QR</h1>
                    <p class="mt-1 text-sm text-base-content/50">Point your camera at the teacher's QR code.</p>
                </div>

                {{-- Result feedback area --}}
                <div id="scan-result" class="hidden"></div>

                {{-- Camera viewport --}}
                <div class="d d2 af-card overflow-hidden !p-0">
                    <div class="px-5 py-4 border-b af-divider">
                        <h2 class="font-semibold text-sm">Camera</h2>
                    </div>
                    <div class="p-4 flex flex-col items-center gap-4">
                        <div id="qr-reader" class="w-full rounded-xl overflow-hidden aspect-square max-w-sm"></div>
                        <p id="camera-status" class="text-sm text-base-content/50 pb-2">Initializing camera…</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @vite('resources/js/qr-scanner.js')
</x-layouts.app>
