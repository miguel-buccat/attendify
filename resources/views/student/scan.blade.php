<x-layouts.app title="Scan QR Code">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="scan" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6 max-w-lg mx-auto">

                <div class="text-center sm:text-left">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold">Scan Attendance QR</h1>
                    <p class="mt-1 text-sm sm:text-base text-base-content/60">Point your camera at the teacher's QR code.</p>
                </div>

                {{-- Result feedback area --}}
                <div id="scan-result" class="hidden"></div>

                {{-- Camera viewport --}}
                <div class="card bg-base-100 rounded-xl border border-base-300">
                    <div class="card-body items-center gap-4 p-3 sm:p-6">
                        <div id="qr-reader" class="w-full rounded-lg overflow-hidden aspect-square max-w-sm"></div>
                        <p id="camera-status" class="text-sm text-base-content/60">Initializing camera…</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @vite('resources/js/qr-scanner.js')
</x-layouts.app>
