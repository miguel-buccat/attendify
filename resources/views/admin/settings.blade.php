<x-layouts.app title="Site Settings">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="settings" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold">Site Settings</h1>
                    <p class="mt-1 text-sm text-base-content/60">Manage institution details and branding.</p>
                </div>

                <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="max-w-2xl space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="card bg-base-100 rounded-xl border border-base-300">
                        <div class="card-body gap-4">
                            <h2 class="card-title text-lg">Institution Details</h2>

                            <x-form.field
                                label="Institution Name"
                                name="institution_name"
                                required
                                :value="$settings['institution_name']"
                            />

                            <label class="form-control w-full">
                                <span class="label-text mb-2">Timezone</span>
                                <select name="timezone" class="select select-bordered w-full @error('timezone') select-error @enderror" required>
                                    @foreach (timezone_identifiers_list() as $tz)
                                        <option value="{{ $tz }}" @selected(old('timezone', $settings['timezone']) === $tz)>
                                            {{ $tz }} (UTC{{ (new DateTimeZone($tz))->getOffset(new DateTime) >= 0 ? '+' : '' }}{{ gmdate('H:i', abs((new DateTimeZone($tz))->getOffset(new DateTime))) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('timezone') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </label>

                            <label class="form-control w-full">
                                <span class="label-text mb-2">Mission Statement</span>
                                <textarea name="mission" rows="3" class="textarea textarea-bordered w-full @error('mission') textarea-error @enderror" maxlength="1000" placeholder="Your institution's mission statement...">{{ old('mission', $settings['mission']) }}</textarea>
                                @error('mission') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </label>

                            <label class="form-control w-full">
                                <span class="label-text mb-2">Vision Statement</span>
                                <textarea name="vision" rows="3" class="textarea textarea-bordered w-full @error('vision') textarea-error @enderror" maxlength="1000" placeholder="Your institution's vision statement...">{{ old('vision', $settings['vision']) }}</textarea>
                                @error('vision') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </label>
                        </div>
                    </div>

                    <div class="card bg-base-100 rounded-xl border border-base-300">
                        <div class="card-body gap-4">
                            <h2 class="card-title text-lg">Branding</h2>

                            <div class="flex items-center gap-4">
                                @if ($settings['institution_logo'])
                                    <div class="size-16 rounded-xl border border-base-300 bg-base-200 p-2 shrink-0">
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

                    <button type="submit" class="btn btn-primary rounded-xl">Save Settings</button>
                </form>

            </div>
        </main>
    </div>
</x-layouts.app>
