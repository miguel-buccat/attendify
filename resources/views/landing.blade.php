<x-layouts.app title="Welcome">
    <style>
        html, body { height: 100%; overflow: hidden; margin: 0; }

        /* ─── Stage: the scroll target that drives everything ─── */
        #scroll-driver {
            position: fixed;
            inset: 0;
            overflow-y: scroll;
            z-index: -1;          /* behind pages */
            opacity: 0;
            pointer-events: none;
        }
        #scroll-track {
            /* height set by JS = sections × 100vh */
        }

        /* ─── Pages stack, fixed ─── */
        #pages {
            position: fixed;
            inset: 0;
            perspective: 1400px;
            perspective-origin: 50% 0%;  /* fold from the top edge */
            overflow: hidden;
        }

        .page {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transform-origin: top center;
            transform-style: preserve-3d;
            backface-visibility: hidden;
            will-change: transform, opacity;
            overflow: hidden;
        }

        /* Each page sits on top of the previous in z-order */
        .page:nth-child(1) { z-index: 10; }
        .page:nth-child(2) { z-index: 9; }
        .page:nth-child(3) { z-index: 8; }
        .page:nth-child(4) { z-index: 7; }
        .page:nth-child(5) { z-index: 6; }

        /* ─── Hero entrance animations ─── */
        @keyframes hero-pop { from { opacity: 0; transform: scale(0.94) translateY(20px); } to { opacity: 1; transform: none; } }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(22px); } to { opacity: 1; transform: none; } }
        @keyframes bgdrift { from { transform: scale(1.12); } to { transform: scale(1); } }
        @keyframes float-idle { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-9px)} }

        .hero-pop  { animation: hero-pop  .9s         cubic-bezier(.16,1,.3,1) both; }
        .hero-d1   { animation: fade-in-up .8s .18s   cubic-bezier(.16,1,.3,1) both; }
        .hero-d2   { animation: fade-in-up .8s .32s   cubic-bezier(.16,1,.3,1) both; }
        .hero-d3   { animation: fade-in-up .8s .48s   cubic-bezier(.16,1,.3,1) both; }
        .hero-d4   { animation: fade-in-up .8s .66s   cubic-bezier(.16,1,.3,1) both; }
        .hero-bg   { animation: bgdrift   2s          ease-out both; }
        .scroll-idle { animation: float-idle 2.2s 1.6s ease-in-out infinite both; }

        /* ─── Page content reveal on activation ─── */
        .page-content {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.55s cubic-bezier(.16,1,.3,1), transform 0.55s cubic-bezier(.16,1,.3,1);
        }
        .page.active .page-content { opacity: 1; transform: none; }
        .page.active .page-content:nth-child(2) { transition-delay: 0.08s; }
        .page.active .page-content:nth-child(3) { transition-delay: 0.16s; }
        .page.active .page-content:nth-child(4) { transition-delay: 0.24s; }

        /* ─── Section background textures ─── */
        .bg-notebook {
            background-image:
                repeating-linear-gradient(transparent, transparent 39px, oklch(var(--bc)/.07) 39px, oklch(var(--bc)/.07) 40px);
            background-size: 100% 40px;
        }
        .bg-notebook::before {
            content: '';
            position: absolute;
            top: 0; bottom: 0;
            left: 64px;
            width: 1px;
            background: oklch(var(--er)/.14);
        }
        .bg-chalkboard {
            background-color: oklch(var(--b2));
            background-image:
                radial-gradient(ellipse at 20% 40%, oklch(var(--p)/.05) 0%, transparent 55%),
                radial-gradient(ellipse at 80% 60%, oklch(var(--s)/.05) 0%, transparent 55%);
        }
        .bg-grid {
            background-image:
                linear-gradient(oklch(var(--bc)/.045) 1px, transparent 1px),
                linear-gradient(90deg, oklch(var(--bc)/.045) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        /* ─── Decorative drifting icons ─── */
        .deco { position: absolute; pointer-events: none; opacity: .05; will-change: transform; }

        /* ─── Feature cards ─── */
        .feature-card {
            transition: transform .35s cubic-bezier(.16,1,.3,1), box-shadow .35s ease;
        }
        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -12px oklch(var(--bc)/.14);
        }

        /* ─── Nav dots ─── */
        .nav-dot { transition: all .3s cubic-bezier(.16,1,.3,1); }
        .nav-dot.active { background: oklch(var(--p)); transform: scaleY(2.8); border-radius: 9999px; }

        /* ─── Page corner shadow (fold hint) ─── */
        .page::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 14px;
            background: linear-gradient(to bottom, oklch(var(--bc)/.1), transparent);
            pointer-events: none;
            z-index: 20;
        }
        .page:nth-child(1)::after { display: none; }

        /* ─── Invisible scroll capture over the fixed stage ─── */
        #scroll-capture {
            position: fixed;
            inset: 0;
            z-index: 100;
            overflow: hidden;
        }
    </style>

    {{-- Fixed theme toggle (above scroll-capture at z-101) --}}
    <button
        type="button"
        onclick="toggleTheme()"
        aria-label="Toggle theme"
        class="fixed top-4 right-4 z-[101] inline-flex items-center justify-center size-9 rounded-xl bg-base-100/80 backdrop-blur border border-base-300/60 text-base-content/60 hover:text-base-content hover:bg-base-100 shadow-sm transition-colors"
    >
        <svg class="theme-toggle-sun size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="1.8"/>
            <path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        <svg class="theme-toggle-moon size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    {{-- The fixed stage where all pages live --}}
    <div id="pages">

        {{-- ══════ PAGE 1: HERO ══════ --}}
        <div class="page bg-base-100" id="page-0" data-index="0">
            @if ($landingBanner)
                <img src="{{ $landingBanner }}" alt="" class="hero-bg absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-base-100/88 via-base-100/72 to-base-100"></div>
            @endif

            {{-- Deco icons --}}
            <svg class="deco top-[10%] left-[7%] size-20" viewBox="0 0 24 24" fill="none"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2V3Zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7V3Z" stroke="currentColor" stroke-width="1.2"/></svg>
            <svg class="deco bottom-[12%] right-[9%] size-16" viewBox="0 0 24 24" fill="none"><path d="M22 10v6M2 10l10-5 10 5-10 5-10-5Z" stroke="currentColor" stroke-width="1.2"/><path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5" stroke="currentColor" stroke-width="1.2"/></svg>
            <svg class="deco top-[15%] right-[14%] size-14" viewBox="0 0 24 24" fill="none"><path d="m4 4 7.07 17 2.51-7.39L21 11.07 4 4Z" stroke="currentColor" stroke-width="1.2"/></svg>

            <div class="relative z-10 text-center px-6 max-w-3xl mx-auto">
                @if ($institutionLogo)
                    <img src="{{ $institutionLogo }}" alt="{{ $institutionName }}" class="hero-pop w-20 h-20 md:w-28 md:h-28 mx-auto mb-6 object-contain drop-shadow-xl">
                @endif
                <h1 class="hero-d1 text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-[1.05] tracking-tight">{{ $institutionName }}</h1>
                <p class="hero-d2 mt-3 text-2xl md:text-3xl font-bold text-primary">Attendance System</p>
                <p class="hero-d3 mt-5 text-base md:text-lg text-base-content/70 max-w-lg mx-auto leading-relaxed">Modern QR-based attendance tracking for educators and students.</p>
                <a href="{{ route('login') }}" class="hero-d4 mt-10 inline-flex items-center justify-center gap-2 rounded-full bg-primary px-12 py-3.5 text-lg font-bold text-primary-content shadow-xl shadow-primary/30 hover:opacity-90 active:scale-[.98] transition-all">
                    Login to Account
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>
        </div>

        {{-- ══════ PAGE 2: VISION (conditional) ══════ --}}
        @if ($institutionVision)
        <div class="page bg-chalkboard" id="page-1" data-index="1">
            {{-- Banner image panel (left side, fades into chalkboard bg) --}}
            @if ($landingBanner)
            <div class="absolute inset-y-0 left-0 w-5/12 hidden md:block z-[1]">
                <img src="{{ $landingBanner }}" alt="" class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-l from-base-200 via-base-200/60 to-transparent"></div>
            </div>
            @endif

            {{-- Corner deco SVGs --}}
            <svg class="deco top-[6%] left-[5%] size-16" viewBox="0 0 24 24" fill="none"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" stroke="currentColor" stroke-width="1"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1"/></svg>
            <svg class="deco bottom-[8%] right-[5%] size-14" viewBox="0 0 24 24" fill="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="1"/></svg>
            {{-- Content pinned to the right --}}
            <div class="absolute inset-y-0 right-0 w-full md:w-7/12 flex items-center z-10">
                <div class="w-full px-10 md:px-16 space-y-8">
                    {{-- Title row --}}
                    <div class="page-content flex items-center gap-5">
                        <div>
                            <p class="text-[10px] uppercase tracking-[.3em] text-secondary font-semibold">Our</p>
                            <h2 class="text-4xl md:text-5xl font-black tracking-tight leading-tight">Vision</h2>
                        </div>
                    </div>

                    {{-- Pull-quote block --}}
                    <div class="page-content relative pl-8 border-l-[3px] border-secondary/30">
                        <p class="text-xl md:text-2xl lg:text-3xl text-base-content/80 leading-relaxed font-medium">{{ $institutionVision }}</p>
                    </div>

                    {{-- Decorative closing rule --}}
                    <div class="page-content flex items-center gap-3">
                        <div class="h-px w-24 bg-gradient-to-r from-secondary/40 to-transparent"></div>
                        <div class="size-1.5 rounded-full bg-secondary/40"></div>
                        <div class="size-1.5 rounded-full bg-secondary/25"></div>
                        <div class="size-1.5 rounded-full bg-secondary/10"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ══════ PAGE 3: MISSION (conditional) ══════ --}}
        @if ($institutionMission)
        <div class="page bg-base-100 bg-notebook" id="page-2" data-index="2">
            {{-- Banner image panel (right side, fades into notebook bg) --}}
            @if ($landingBanner)
            <div class="absolute inset-y-0 right-0 w-5/12 hidden md:block z-[1]">
                <img src="{{ $landingBanner }}" alt="" class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-base-100 via-base-100/60 to-transparent"></div>
            </div>
            @endif

            {{-- Corner deco SVGs --}}
            <svg class="deco top-[6%] right-[5%] size-16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1"/><circle cx="12" cy="12" r="6" stroke="currentColor" stroke-width="1"/><circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="1"/></svg>
            <svg class="deco bottom-[8%] left-[8%] size-14" viewBox="0 0 24 24" fill="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="1"/></svg>

            {{-- Content pinned to the left --}}
            <div class="absolute inset-y-0 left-0 w-full md:w-7/12 flex items-center z-10">
                <div class="w-full px-10 md:px-16 space-y-8">
                    {{-- Title row --}}
                    <div class="page-content flex items-center gap-5">
                        <div>
                            <p class="text-[10px] uppercase tracking-[.3em] text-primary font-semibold">Our</p>
                            <h2 class="text-4xl md:text-5xl font-black tracking-tight leading-tight">Mission</h2>
                        </div>
                    </div>

                    {{-- Pull-quote block --}}
                    <div class="page-content relative pl-8 border-l-[3px] border-primary/30">
                        <p class="text-xl md:text-2xl lg:text-3xl text-base-content/80 leading-relaxed font-medium">{{ $institutionMission }}</p>
                    </div>

                    {{-- Decorative closing rule --}}
                    <div class="page-content flex items-center gap-3">
                        <div class="h-px w-24 bg-gradient-to-r from-primary/40 to-transparent"></div>
                        <div class="size-1.5 rounded-full bg-primary/40"></div>
                        <div class="size-1.5 rounded-full bg-primary/25"></div>
                        <div class="size-1.5 rounded-full bg-primary/10"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ══════ PAGE 4: ABOUT ══════ --}}
        <div class="page bg-base-100 bg-grid" id="page-3" data-index="3">
            <div class="relative max-w-5xl mx-auto px-6 z-10 w-full">
                <div class="text-center space-y-3 md:space-y-5">
                    <div class="page-content">
                        <img src="{{ asset('assets/attendify.png') }}" alt="Attendify" class="w-14 h-14 md:w-24 md:h-24 mx-auto mb-2 md:mb-4 object-contain drop-shadow-lg">
                    </div>
                    <div class="page-content">
                        <h2 class="text-3xl md:text-5xl lg:text-6xl font-black tracking-tight">About Attendify</h2>
                        <div class="mt-2 md:mt-3 mx-auto w-16 h-1 rounded-full bg-accent/40"></div>
                    </div>
                    <p class="page-content text-sm md:text-xl text-base-content/75 leading-relaxed max-w-2xl mx-auto">
                        A modern, QR-based attendance management system designed to make tracking student attendance seamless and efficient — so educators can focus on what matters most.
                    </p>
                </div>
                <div class="page-content mt-6 md:mt-14 grid grid-cols-3 gap-3 md:gap-5">
                    <div class="feature-card p-4 md:p-7 rounded-2xl md:rounded-3xl bg-base-100 border border-base-300/50 text-center">
                        <div class="inline-flex items-center justify-center size-10 md:size-14 rounded-xl md:rounded-2xl bg-primary/10 text-primary mb-3 md:mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 md:size-7"><path d="M3 7V5a2 2 0 0 1 2-2h2m10 0h2a2 2 0 0 1 2 2v2m0 10v2a2 2 0 0 1-2 2h-2M3 17v2a2 2 0 0 0 2 2h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 12h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </div>
                        <h3 class="font-bold text-xs md:text-lg">QR Scanning</h3>
                        <p class="mt-1 md:mt-2 text-xs text-base-content/65 leading-relaxed hidden md:block">Instant attendance capture via mobile QR. No manual roll calls.</p>
                    </div>
                    <div class="feature-card p-4 md:p-7 rounded-2xl md:rounded-3xl bg-base-100 border border-base-300/50 text-center">
                        <div class="inline-flex items-center justify-center size-10 md:size-14 rounded-xl md:rounded-2xl bg-secondary/10 text-secondary mb-3 md:mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 md:size-7"><path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <h3 class="font-bold text-xs md:text-lg">Live Tracking</h3>
                        <p class="mt-1 md:mt-2 text-xs text-base-content/65 leading-relaxed hidden md:block">Real-time session monitoring with auto-updating attendance lists.</p>
                    </div>
                    <div class="feature-card p-4 md:p-7 rounded-2xl md:rounded-3xl bg-base-100 border border-base-300/50 text-center">
                        <div class="inline-flex items-center justify-center size-10 md:size-14 rounded-xl md:rounded-2xl bg-accent/10 text-accent mb-3 md:mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 md:size-7"><path d="M12 2 2 7l10 5 10-5-10-5ZM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <h3 class="font-bold text-xs md:text-lg">Smart Dashboards</h3>
                        <p class="mt-1 md:mt-2 text-xs text-base-content/65 leading-relaxed hidden md:block">Role-based analytics and insights for every user type.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════ PAGE 5: FOOTER ══════ --}}
        <div class="page bg-base-200" id="page-4" data-index="4" style="align-items:center;min-height:auto;height:100vh;">
            <div class="z-10 text-center space-y-4 px-6">
                <img src="{{ asset('assets/attendify.png') }}" alt="Attendify" class="w-10 h-10 mx-auto object-contain opacity-50">
                <p class="text-sm text-base-content/55">
                    Attendify licensed under the MIT License.<br>Copyright &copy; {{ now()->year }} Attendify Developers.
                </p>
            </div>
        </div>
    </div>

    {{-- Side navigation dots --}}
    <nav class="hidden md:flex fixed right-6 top-1/2 -translate-y-1/2 z-[200] flex-col items-center gap-3" aria-label="Page sections">
        <button data-page="0" class="nav-dot w-2 h-2 rounded-full bg-base-content/20 active" aria-label="Hero"></button>
        @if ($institutionVision)<button data-page="1" class="nav-dot w-2 h-2 rounded-full bg-base-content/20" aria-label="Vision"></button>@endif
        @if ($institutionMission)<button data-page="2" class="nav-dot w-2 h-2 rounded-full bg-base-content/20" aria-label="Mission"></button>@endif
        <button data-page="3" class="nav-dot w-2 h-2 rounded-full bg-base-content/20" aria-label="About"></button>
        <button data-page="4" class="nav-dot w-2 h-2 rounded-full bg-base-content/20" aria-label="Footer"></button>
    </nav>

    <script>
    (() => {
        const pages = Array.from(document.querySelectorAll('.page'));
        const dots  = Array.from(document.querySelectorAll('.nav-dot'));
        const N     = pages.length;

        let current = 0;          // active page index
        let transitioning = false;

        // ── easing ──────────────────────────────────────────
        function easeOutExpo(t) { return t === 1 ? 1 : 1 - Math.pow(2, -10 * t); }
        function easeInExpo(t)  { return t === 0 ? 0 : Math.pow(2, 10 * t - 10); }

        // ── Apply fold transform to a page ──────────────────
        // progress 0 = folded (flat, hidden behind, about to fold in from top)
        // progress 1 = fully visible (flat, covering the screen)
        // We fold OUTWARD from the top: rotateX(deg) around top edge so the bottom swings away
        function setPageTransform(page, progress) {
            // Exit animation (page LEAVING — fold upward from bottom, top stays)
            // progress = 1 means fully visible, 0 means fully folded up and gone
            const deg = (1 - progress) * 88;  // 0°=flat, 88°=nearly vertical folded back
            const opac = progress < 0.25 ? progress * 4 : 1;

            // Shadow intensity increases as page peels down from above
            const shadowAlpha = Math.max(0, 1 - progress) * 0.45;

            page.style.transform = `perspective(1400px) rotateX(${-deg}deg)`;
            page.style.transformOrigin = 'top center';
            page.style.opacity = opac;
            page.style.boxShadow = `0 ${30 * (1-progress)}px ${60 * (1-progress)}px rgba(0,0,0,${shadowAlpha})`;
        }

        // ── Page state management ────────────────────────────
        function initPages() {
            pages.forEach((p, i) => {
                if (i === 0) {
                    // First page: fully visible at top, folded pages below
                    setPageTransform(p, 1);
                    p.classList.add('active');
                } else {
                    // All other pages: folded up (behind), rotated 88°
                    setPageTransform(p, 0);
                    p.classList.remove('active');
                }
            });
        }

        // ── Animate page transition ──────────────────────────
        function goTo(to) {
            if (transitioning || to === current || to < 0 || to >= N) return;
            transitioning = true;

            const from = current;
            const forward = to > from;
            const duration = 700; // ms
            let startTime = null;

            // The page being revealed (comes from behind folded, unfolds)
            const incoming = pages[to];
            // The page being hidden (currently flat, folds upward/away)
            const outgoing = pages[from];

            incoming.classList.remove('active');
            if (forward) {
                // Outgoing (current) is on top, folds upward; incoming revealed below
                outgoing.style.zIndex = 20;
                incoming.style.zIndex = 19;
            } else {
                // Incoming (previous page) drops down on top to cover outgoing
                incoming.style.zIndex = 20;
                outgoing.style.zIndex = 19;
            }

            // Activate nav dot
            dots.forEach((d, i) => d.classList.toggle('active', i === to));

            function tick(ts) {
                if (!startTime) startTime = ts;
                const elapsed = ts - startTime;
                const raw = Math.min(elapsed / duration, 1);

                if (forward) {
                    // Going forward: outgoing folds UP (rotates away), incoming is revealed beneath
                    const outProgress = 1 - easeInExpo(raw);          // 1→0
                    const inProgress  = easeOutExpo(raw);              // 0→1
                    setPageTransform(outgoing, outProgress);
                    setPageTransform(incoming, inProgress);
                } else {
                    // Going back: incoming (previous page) drops DOWN from folded position (0→1)
                    // outgoing stays flat behind it while being covered
                    const inProgress = easeOutExpo(raw);               // 0→1
                    setPageTransform(incoming, inProgress);
                    // keep outgoing flat at progress=1 while incoming overlays it
                }

                if (raw < 1) {
                    requestAnimationFrame(tick);
                } else {
                    // Finalize: incoming is always the new active page, outgoing always hidden
                    setPageTransform(incoming, 1);
                    setPageTransform(outgoing, 0);
                    incoming.classList.add('active');
                    outgoing.classList.remove('active');
                    // Reset inline z-index so static CSS stacking takes over
                    incoming.style.zIndex = '';
                    outgoing.style.zIndex = '';
                    current = to;
                    transitioning = false;
                }
            }

            requestAnimationFrame(tick);
        }

        // ── Input handling ───────────────────────────────────
        let wheelAccum = 0;
        const WHEEL_THRESH = 60;

        window.addEventListener('wheel', (e) => {
            e.preventDefault();
            wheelAccum += e.deltaY;
            if (wheelAccum > WHEEL_THRESH) {
                wheelAccum = 0;
                goTo(current + 1);
            } else if (wheelAccum < -WHEEL_THRESH) {
                wheelAccum = 0;
                goTo(current - 1);
            }
        }, { passive: false });

        // Touch support
        let touchStartY = null;
        window.addEventListener('touchstart', (e) => { touchStartY = e.touches[0].clientY; }, { passive: true });
        window.addEventListener('touchend', (e) => {
            if (touchStartY === null) return;
            const dy = touchStartY - e.changedTouches[0].clientY;
            if (Math.abs(dy) > 40) goTo(dy > 0 ? current + 1 : current - 1);
            touchStartY = null;
        }, { passive: true });

        // Keyboard
        window.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown' || e.key === 'PageDown') { e.preventDefault(); goTo(current + 1); }
            if (e.key === 'ArrowUp'   || e.key === 'PageUp')   { e.preventDefault(); goTo(current - 1); }
        });

        // Nav dots
        dots.forEach((dot, i) => {
            dot.addEventListener('click', () => goTo(i));
        });

        // ── Init ─────────────────────────────────────────────
        initPages();
    })();
    </script>
</x-layouts.app>
