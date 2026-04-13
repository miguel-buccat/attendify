<x-layouts.app title="System Setup — Attendify">
    <style>
        @keyframes rise-up { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:none; } }
        @keyframes blob-drift {
            0%,100% { transform: translate(0,0) scale(1); }
            40%      { transform: translate(-14px,10px) scale(1.06); }
            70%      { transform: translate(10px,-8px) scale(.96); }
        }
        .r { animation: rise-up .55s cubic-bezier(.16,1,.3,1) both; }
        .r1 { animation-delay: .00s; } .r2 { animation-delay: .10s; } .r3 { animation-delay: .20s; }
        .blob-a { animation: blob-drift 11s ease-in-out infinite; }
        .blob-b { animation: blob-drift 14s ease-in-out infinite reverse; animation-delay: 3s; }
    </style>

    <main class="relative min-h-screen flex flex-col bg-base-200 overflow-hidden">
        {{-- Background blobs --}}
        <div aria-hidden="true" class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="blob-a absolute -top-20 -left-16 size-80 rounded-full bg-primary/12 blur-3xl"></div>
            <div class="blob-b absolute bottom-10 -right-16 size-72 rounded-full bg-secondary/10 blur-3xl"></div>
            <div class="absolute inset-0" style="background-image:radial-gradient(circle,oklch(var(--bc)/.04) 1px,transparent 1px);background-size:24px 24px;"></div>
        </div>

        {{-- Top bar --}}
        <header class="relative z-10 flex items-center justify-between px-6 py-4 md:px-10">
            <a href="{{ route('new.index') }}" class="flex items-center gap-2 text-base-content/60 hover:text-base-content transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M19 12H5m7-7-7 7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span class="text-sm font-semibold">Back</span>
            </a>
            <div class="flex items-center gap-2">
                <span class="text-sm font-black">Attendify</span>
            </div>
        </header>

        {{-- Main content --}}
        <div class="relative z-10 flex-1 flex items-center justify-center px-4 py-8 md:py-12">
            <div class="w-full max-w-lg space-y-5">

                {{-- Page heading --}}
                <div class="r r1 text-center space-y-1.5">
                    <div class="inline-flex items-center gap-2 rounded-full bg-primary/10 border border-primary/20 px-3.5 py-1.5 text-xs font-bold uppercase tracking-widest text-primary mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/><path d="M12 2v2m0 16v2M4.22 4.22l1.42 1.42m12.72 12.72 1.42 1.42M2 12h2m16 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        System Setup
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight">Get started</h1>
                    <p class="text-sm text-base-content/50">Two quick steps and you're live.</p>
                </div>

                @if (session('status'))
                    <div class="r r2">
                        <x-alert>{{ session('status') }}</x-alert>
                    </div>
                @endif

                {{-- Progress stepper --}}
                <div class="r r2 flex items-center gap-0 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                    {{-- Step 1 --}}
                    <div class="flex-1 flex items-center gap-3 px-5 py-4 {{ ! $hasAdmin ? 'bg-primary/5 border-r border-primary/15' : 'border-r border-base-300/30' }}">
                        <div class="size-8 rounded-full {{ ! $hasAdmin ? 'bg-primary text-primary-content shadow-md shadow-primary/30' : ($hasAdmin ? 'bg-success/15 text-success' : 'bg-base-200 text-base-content/30') }} flex items-center justify-center text-xs font-black shrink-0 transition-all">
                            @if ($hasAdmin)
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @else
                                1
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-bold {{ ! $hasAdmin ? 'text-primary' : 'text-base-content/50' }}">Step 1</p>
                            <p class="text-xs font-semibold {{ ! $hasAdmin ? 'text-base-content' : 'text-base-content/40' }}">Admin Account</p>
                        </div>
                    </div>
                    {{-- Step 2 --}}
                    <div class="flex-1 flex items-center gap-3 px-5 py-4 {{ $hasAdmin ? 'bg-primary/5' : '' }}">
                        <div class="size-8 rounded-full {{ $hasAdmin ? 'bg-primary text-primary-content shadow-md shadow-primary/30' : 'bg-base-200 text-base-content/30' }} flex items-center justify-center text-xs font-black shrink-0 transition-all">
                            2
                        </div>
                        <div>
                            <p class="text-xs font-bold {{ $hasAdmin ? 'text-primary' : 'text-base-content/35' }}">Step 2</p>
                            <p class="text-xs font-semibold {{ $hasAdmin ? 'text-base-content' : 'text-base-content/30' }}">Institution</p>
                        </div>
                    </div>
                </div>

                {{-- Form card --}}
                <div class="r r3 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden shadow-sm">
                    @if (! $hasAdmin)
                        {{-- Step 1 header --}}
                        <div class="px-6 py-5 border-b border-base-300/30 flex items-center gap-3">
                            <div class="size-9 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4.5 text-primary"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div>
                                <h2 class="font-bold text-sm">Create Admin Account</h2>
                                <p class="text-xs text-base-content/45 mt-0.5">This will be the superuser of the system.</p>
                            </div>
                        </div>

                        <div class="px-6 py-6">
                            <form method="POST" action="{{ route('new.setup.admin') }}" class="space-y-4">
                                @csrf
                                <x-form.field label="Full Name" name="name" required placeholder="e.g. Dr. Maria Santos" />
                                <x-form.field label="Email Address" name="email" type="email" required placeholder="admin@school.edu" />
                                <x-form.field label="Password" name="password" type="password" required />
                                <x-form.field label="Confirm Password" name="password_confirmation" type="password" required />

                                <div class="pt-2">
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold text-primary-content shadow-lg shadow-primary/25 hover:opacity-90 active:scale-[.98] transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Create Admin Account
                                    </button>
                                </div>
                            </form>
                        </div>

                    @else
                        {{-- Step 2 header --}}
                        <div class="px-6 py-5 border-b border-base-300/30 flex items-center gap-3">
                            <div class="size-9 rounded-xl bg-secondary/10 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4.5 text-secondary"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 22V12h6v10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div>
                                <h2 class="font-bold text-sm">Institution Settings</h2>
                                <p class="text-xs text-base-content/45 mt-0.5">Customize Attendify for your school.</p>
                            </div>
                        </div>

                        <div class="px-6 py-6">
                            <form method="POST" action="{{ route('new.setup.settings') }}" enctype="multipart/form-data" class="space-y-5">
                                @csrf

                                <x-form.field
                                    label="Institution Name"
                                    name="institution_name"
                                    required
                                    placeholder="e.g. Riverside Academy"
                                    :value="$siteSettings->get('institution_name')"
                                />

                                <div>
                                    <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5">
                                        Timezone <span class="text-error">*</span>
                                    </label>
                                    <select name="timezone" class="w-full rounded-xl border {{ $errors->has('timezone') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40" required>
                                        @foreach (timezone_identifiers_list() as $tz)
                                            <option value="{{ $tz }}" @selected(old('timezone', $siteSettings->get('timezone', 'Asia/Manila')) === $tz)>
                                                {{ $tz }} (UTC{{ (new DateTimeZone($tz))->getOffset(new DateTime) >= 0 ? '+' : '' }}{{ gmdate('H:i', abs((new DateTimeZone($tz))->getOffset(new DateTime))) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('timezone') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <x-form.field
                                        label="Institution Logo"
                                        name="institution_logo"
                                        type="file"
                                        required
                                        accept="image/*"
                                        hint="Square ratio recommended."
                                    />
                                    <x-form.field
                                        label="Landing Banner"
                                        name="landing_banner"
                                        type="file"
                                        required
                                        accept="image/*"
                                        hint="Wide banner image."
                                    />
                                </div>

                                <details class="group rounded-xl border border-base-300/40 bg-base-200/20 overflow-hidden">
                                    <summary class="flex items-center justify-between gap-3 px-4 py-3 cursor-pointer select-none [&::-webkit-details-marker]:hidden hover:bg-base-200/50 transition-colors">
                                        <div class="flex items-center gap-2.5">
                                            <div class="size-6 rounded-lg bg-base-200 flex items-center justify-center shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5 text-base-content/40"><path d="M12 3v1m0 16v1M4.22 4.22l.707.707M18.364 18.364l.707.707M3 12h1m16 0h1M4.927 19.073l.707-.707M18.364 5.636l.707-.707" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.8"/></svg>
                                            </div>
                                            <span class="text-xs font-bold uppercase tracking-widest text-base-content/40">Optional Details</span>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4 text-base-content/30 transition-transform duration-200 group-open:rotate-180 shrink-0"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </summary>
                                    <div class="px-4 pb-4 pt-3 space-y-4 border-t border-base-300/30">
                                        <x-form.field
                                            label="Vision Statement"
                                            name="vision"
                                            :value="$siteSettings->get('vision')"
                                            placeholder="We envision..."
                                        />
                                        <x-form.field
                                            label="Mission Statement"
                                            name="mission"
                                            :value="$siteSettings->get('mission')"
                                            placeholder="Our mission is..."
                                        />
                                    </div>
                                </details>

                                <div class="pt-2">
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold text-primary-content shadow-lg shadow-primary/25 hover:opacity-90 active:scale-[.98] transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4.5"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Finish Setup &amp; Launch
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <footer class="relative z-10 text-center py-4 text-xs text-base-content/30">
            Attendify &mdash; MIT License &copy; {{ now()->year }}
        </footer>
    </main>
</x-layouts.app>

