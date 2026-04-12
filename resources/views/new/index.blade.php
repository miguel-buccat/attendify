<x-layouts.app>
    <style>
        @keyframes float-a {
            0%,100% { transform: translate(0,0) scale(1); }
            40%      { transform: translate(-18px, 14px) scale(1.06); }
            70%      { transform: translate(12px,-10px) scale(.96); }
        }
        @keyframes float-b {
            0%,100% { transform: translate(0,0) scale(1); }
            33%      { transform: translate(14px, -18px) scale(1.04); }
            66%      { transform: translate(-10px, 12px) scale(.97); }
        }
        @keyframes float-c {
            0%,100% { transform: translate(0,0) scale(1); }
            50%      { transform: translate(-8px, -14px) scale(1.05); }
        }
        @keyframes rise { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:none; } }
        @keyframes fade-in { from { opacity:0; } to { opacity:1; } }
        .rise-1 { animation: rise .7s cubic-bezier(.16,1,.3,1) .05s both; }
        .rise-2 { animation: rise .7s cubic-bezier(.16,1,.3,1) .18s both; }
        .rise-3 { animation: rise .7s cubic-bezier(.16,1,.3,1) .3s both; }
        .rise-4 { animation: rise .7s cubic-bezier(.16,1,.3,1) .42s both; }
        .rise-5 { animation: rise .7s cubic-bezier(.16,1,.3,1) .54s both; }
        .fade-bg { animation: fade-in 1.2s ease both; }
        .blob-a { animation: float-a 12s ease-in-out infinite; }
        .blob-b { animation: float-b 15s ease-in-out infinite; animation-delay: 2s; }
        .blob-c { animation: float-c 10s ease-in-out infinite; animation-delay: 4s; }
        .feature-card { transition: transform .2s ease, box-shadow .2s ease; }
        .feature-card:hover { transform: translateY(-3px); }
    </style>

    <main class="relative min-h-screen flex flex-col bg-base-200 overflow-hidden">

        {{-- Ambient background blobs --}}
        <div aria-hidden="true" class="fade-bg pointer-events-none fixed inset-0 overflow-hidden">
            <div class="blob-a absolute -top-32 -left-24 size-[36rem] rounded-full bg-primary/15 blur-3xl"></div>
            <div class="blob-b absolute top-1/3 -right-32 size-[30rem] rounded-full bg-secondary/12 blur-3xl"></div>
            <div class="blob-c absolute bottom-0 left-1/4 size-64 rounded-full bg-accent/10 blur-2xl"></div>
            <div class="absolute inset-0" style="background-image:radial-gradient(circle,oklch(var(--bc)/.04) 1px,transparent 1px);background-size:28px 28px;"></div>
        </div>

        {{-- Header bar --}}
        <header class="relative z-10 flex items-center justify-between px-6 py-4 md:px-10">
            <div class="flex items-center gap-2.5">
                <span class="font-black text-sm tracking-tight">Attendify</span>
            </div>
            <a href="{{ route('login') }}" class="text-sm font-semibold text-base-content/60 hover:text-base-content transition-colors">
                Sign in →
            </a>
        </header>

        {{-- Hero --}}
        <section class="relative z-10 flex-1 flex flex-col lg:flex-row items-center gap-12 lg:gap-20 px-6 py-12 md:px-10 lg:px-16 xl:px-24 max-w-7xl mx-auto w-full">

            {{-- Left: copy + CTA --}}
            <div class="flex-1 flex flex-col items-start gap-6 max-w-xl">
                <h1 class="rise-2 text-5xl md:text-6xl lg:text-7xl font-black leading-[1.02] tracking-tight">
                    <span class="block text-base-content">{{ $institutionName }}</span>
                    <span class="block text-primary">Attendance</span>
                    <span class="block text-base-content/30">System</span>
                </h1>

                <p class="rise-3 text-base md:text-lg text-base-content/60 leading-relaxed">
                    QR-powered, real-time attendance management for modern educational institutions. Track presence, manage absence notifications, and generate reports — all in one place.
                </p>

                <div class="rise-4 flex flex-wrap items-center gap-3">
                    <a href="{{ route('new.setup') }}" class="inline-flex items-center gap-2.5 rounded-2xl bg-primary px-7 py-3.5 text-base font-bold text-primary-content shadow-lg shadow-primary/30 hover:opacity-90 active:scale-[.98] transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5">
                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 2v2m0 16v2M4.22 4.22l1.42 1.42m12.72 12.72 1.42 1.42M2 12h2m16 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Configure System
                    </a>
                    <span class="text-sm text-base-content/40 font-medium">Takes about 2 minutes</span>
                </div>

                {{-- Trust badges --}}
                <div class="rise-5 flex flex-wrap items-center gap-4 pt-2">
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-base-content/45">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5 text-success"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        QR Attendance
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-base-content/45">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5 text-success"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Parent Notifications
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-base-content/45">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5 text-success"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Weekly Reports
                    </div>
                </div>
            </div>

            {{-- Right: feature cards orb --}}
            <div class="flex-1 relative flex items-center justify-center min-h-[420px] lg:min-h-[520px] w-full max-w-lg lg:max-w-none">
                {{-- Institution logo center orb --}}
                <div class="absolute size-28 rounded-3xl bg-base-100 border border-base-300/50 shadow-xl flex items-center justify-center z-20 p-4">
                    <img src="{{ $institutionLogo }}" alt="{{ $institutionName }}" class="size-full object-contain">
                </div>

                {{-- Feature cards orbiting --}}
                <div class="feature-card absolute top-4 left-8 lg:top-8 lg:left-12 z-10 flex items-center gap-3 rounded-2xl border border-base-300/50 bg-base-100/90 backdrop-blur-sm px-4 py-3 shadow-lg">
                    <div class="size-9 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4.5 text-primary"><path d="M3 7V5a2 2 0 0 1 2-2h2m10 0h2a2 2 0 0 1 2 2v2m0 10v2a2 2 0 0 1-2 2h-2M3 17v2a2 2 0 0 0 2 2h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><rect x="7" y="7" width="10" height="10" rx="1" stroke="currentColor" stroke-width="1.8"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-base-content">QR Scanner</p>
                        <p class="text-[10px] text-base-content/50">Scan in seconds</p>
                    </div>
                </div>

                <div class="feature-card absolute top-4 right-4 lg:top-10 lg:right-0 z-10 flex items-center gap-3 rounded-2xl border border-base-300/50 bg-base-100/90 backdrop-blur-sm px-4 py-3 shadow-lg">
                    <div class="size-9 rounded-xl bg-success/10 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4.5 text-success"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-base-content">Present</p>
                        <p class="text-[10px] text-base-content/50">Just now</p>
                    </div>
                </div>

                <div class="feature-card absolute bottom-16 left-4 lg:bottom-20 lg:left-8 z-10 flex items-center gap-3 rounded-2xl border border-base-300/50 bg-base-100/90 backdrop-blur-sm px-4 py-3 shadow-lg">
                    <div class="size-9 rounded-xl bg-secondary/10 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4.5 text-secondary"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m22 6-10 7L2 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-base-content">Parent Email</p>
                        <p class="text-[10px] text-base-content/50">Auto-notified</p>
                    </div>
                </div>

                <div class="feature-card absolute bottom-8 right-6 lg:bottom-14 lg:right-4 z-10 flex items-center gap-3 rounded-2xl border border-base-300/50 bg-base-100/90 backdrop-blur-sm px-4 py-3 shadow-lg">
                    <div class="size-9 rounded-xl bg-warning/10 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4.5 text-warning"><path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-base-content">Analytics</p>
                        <p class="text-[10px] text-base-content/50">Live charts</p>
                    </div>
                </div>

                {{-- Connection lines (decorative) --}}
                <div class="absolute inset-0 pointer-events-none z-0">
                    <svg class="size-full opacity-10" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="200" cy="200" r="100" stroke="oklch(var(--p))" stroke-width="1" stroke-dasharray="4 6"/>
                        <circle cx="200" cy="200" r="160" stroke="oklch(var(--p))" stroke-width="1" stroke-dasharray="2 8"/>
                    </svg>
                </div>
            </div>
        </section>

        <footer class="relative z-10 border-t border-base-300/30 px-6 py-4 md:px-10 text-xs text-base-content/35 flex items-center justify-between">
            <span>Attendify licensed under the MIT License. Copyright &copy; {{ now()->year }} Attendify Developers.</span>
        </footer>
    </main>
</x-layouts.app>
