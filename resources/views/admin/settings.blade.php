<x-layouts.app title="Site Settings">
    <style>
        @keyframes d-up { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .d { animation: d-up .45s cubic-bezier(.16,1,.3,1) both; }
        .d1 { animation-delay: .00s; } .d2 { animation-delay: .07s; } .d3 { animation-delay: .14s; }
        .d4 { animation-delay: .21s; } .d5 { animation-delay: .28s; }
    </style>
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="settings" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                <div class="d d1">
                    <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Admin</p>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight">Site Settings</h1>
                    <p class="mt-1 text-sm text-base-content/50">Manage institution details and branding.</p>
                </div>

                <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="max-w-2xl space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="d d2 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                        <div class="px-5 py-4 border-b border-base-300/30">
                            <h2 class="font-semibold text-sm">Institution Details</h2>
                        </div>
                        <div class="px-5 py-5 space-y-4">

                            <x-form.field
                                label="Institution Name"
                                name="institution_name"
                                required
                                :value="$settings['institution_name']"
                            />

                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5">Timezone</label>
                                <select name="timezone" class="w-full rounded-xl border {{ $errors->has('timezone') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40" required>
                                    @foreach (timezone_identifiers_list() as $tz)
                                        <option value="{{ $tz }}" @selected(old('timezone', $settings['timezone']) === $tz)>
                                            {{ $tz }} (UTC{{ (new DateTimeZone($tz))->getOffset(new DateTime) >= 0 ? '+' : '' }}{{ gmdate('H:i', abs((new DateTimeZone($tz))->getOffset(new DateTime))) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('timezone') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5">Mission Statement</label>
                                <textarea name="mission" rows="3" class="w-full rounded-xl border {{ $errors->has('mission') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40" maxlength="1000" placeholder="Your institution's mission statement...">{{ old('mission', $settings['mission']) }}</textarea>
                                @error('mission') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5">Vision Statement</label>
                                <textarea name="vision" rows="3" class="w-full rounded-xl border {{ $errors->has('vision') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40" maxlength="1000" placeholder="Your institution's vision statement...">{{ old('vision', $settings['vision']) }}</textarea>
                                @error('vision') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </div>

                        </div>
                    </div>

                    <div class="d d3 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                        <div class="px-5 py-4 border-b border-base-300/30">
                            <h2 class="font-semibold text-sm">Branding</h2>
                        </div>
                        <div class="px-5 py-5 space-y-4">

                            <div class="flex items-center gap-4">
                                @if ($settings['institution_logo'])
                                    <div class="size-16 rounded-xl border border-base-300/50 bg-base-200 p-2 shrink-0">
                                        <img src="{{ $settings['institution_logo'] }}" alt="Current logo" class="h-full w-full object-contain">
                                    </div>
                                @endif
                                <x-form.field
                                    label="Institution Logo"
                                    name="institution_logo"
                                    type="file"
                                    accept="image/*"
                                    hint="Leave empty to keep current. Expected 1:1 aspect ratio."
                                />
                            </div>

                            <x-form.field
                                label="Landing Banner"
                                name="landing_banner"
                                type="file"
                                accept="image/*"
                                hint="Leave empty to keep current."
                            />

                        </div>
                    </div>

                    <div class="d d4">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-primary-content text-sm font-semibold hover:opacity-90 transition-opacity">
                            Save Settings
                        </button>
                    </div>

                </form>

            </div>
        </main>
    </div>
</x-layouts.app>
