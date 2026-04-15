<x-layouts.app title="Site Settings">
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

                <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 items-start">

                        {{-- Institution Details --}}
                        <div class="d d2 af-card overflow-hidden !p-0">
                            <div class="px-5 py-4 border-b af-divider">
                                <h2 class="font-semibold text-sm">Institution Details</h2>
                            </div>
                            <div class="px-5 py-5 space-y-4">

                                <x-form.field
                                    label="Institution Name"
                                    name="institution_name"
                                    required
                                    :value="$settings['institution_name']"
                                />

                                <x-form.field name="timezone" label="Timezone" required>
                                    <select name="timezone" class="af-input @error('timezone') af-input-error @enderror" required>
                                        @foreach (timezone_identifiers_list() as $tz)
                                            <option value="{{ $tz }}" @selected(old('timezone', $settings['timezone']) === $tz)>
                                                {{ $tz }} (UTC{{ (new DateTimeZone($tz))->getOffset(new DateTime) >= 0 ? '+' : '' }}{{ gmdate('H:i', abs((new DateTimeZone($tz))->getOffset(new DateTime))) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </x-form.field>

                                <x-form.field name="mission" label="Mission Statement">
                                    <textarea name="mission" rows="3" class="af-input @error('mission') af-input-error @enderror" maxlength="1000" placeholder="Your institution's mission statement...">{{ old('mission', $settings['mission']) }}</textarea>
                                </x-form.field>

                                <x-form.field name="vision" label="Vision Statement">
                                    <textarea name="vision" rows="3" class="af-input @error('vision') af-input-error @enderror" maxlength="1000" placeholder="Your institution's vision statement...">{{ old('vision', $settings['vision']) }}</textarea>
                                </x-form.field>

                            </div>
                        </div>

                        {{-- Branding --}}
                        <div class="d d3 af-card overflow-hidden !p-0">
                            <div class="px-5 py-4 border-b af-divider">
                                <h2 class="font-semibold text-sm">Branding</h2>
                            </div>
                            <div class="px-5 py-5 space-y-5">

                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 mb-2">Institution Logo</p>
                                    <div class="flex items-center gap-4">
                                        @if ($settings['institution_logo'])
                                            <div class="size-16 rounded-xl border border-base-300/50 bg-base-200 p-2 shrink-0">
                                                <img src="{{ $settings['institution_logo'] }}" alt="Current logo" class="h-full w-full object-contain">
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <x-form.field
                                                label=""
                                                name="institution_logo"
                                                type="file"
                                                accept="image/*"
                                                hint="Leave empty to keep current. Expected 1:1 aspect ratio."
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 mb-2">Landing Banner</p>
                                    @if ($settings['landing_banner'])
                                        <div class="w-full rounded-xl border border-base-300/50 bg-base-200 p-2 mb-3 overflow-hidden">
                                            <img src="{{ $settings['landing_banner'] }}" alt="Current banner" class="w-full h-24 object-cover rounded-lg">
                                        </div>
                                    @endif
                                    <x-form.field
                                        label=""
                                        name="landing_banner"
                                        type="file"
                                        accept="image/*"
                                        hint="Leave empty to keep current. Wide format (16:9) recommended."
                                    />
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="d d4">
                        <x-ui.button type="submit" variant="primary">Save Settings</x-ui.button>
                    </div>

                </form>

            </div>
        </main>
    </div>
</x-layouts.app>
