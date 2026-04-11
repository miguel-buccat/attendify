<x-layouts.app title="System Setup">
    <main class="min-h-screen grid place-items-center p-6 md:p-10">
        <section class="w-full max-w-3xl card bg-base-100 shadow-xl border border-base-300/70">
            <div class="card-body gap-6">
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold">System Setup</h1>
                    <p class="text-base-content/70">Complete the 2 setup steps before using Attendify.</p>
                </div>

                @if (session('status'))
                    <x-alert>{{ session('status') }}</x-alert>
                @endif

                <ul class="steps steps-vertical md:steps-horizontal w-full">
                    <li class="step step-primary">Create admin account</li>
                    <li class="step {{ $hasAdmin ? 'step-primary' : '' }}">Set institution settings</li>
                </ul>

                @if (! $hasAdmin)
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold">Step 1: Create Admin Account</h2>

                        <form method="POST" action="{{ route('new.setup.admin') }}" class="grid gap-4">
                            @csrf

                            <x-form.field label="Name" name="name" required />
                            <x-form.field label="Email" name="email" type="email" required />
                            <x-form.field label="Password" name="password" type="password" required />
                            <x-form.field label="Confirm Password" name="password_confirmation" type="password" required />

                            <button type="submit" class="btn btn-primary rounded-md w-full md:w-auto">Create Admin Account</button>
                        </form>
                    </div>
                @else
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold">Step 2: Site Settings</h2>

                        <form method="POST" action="{{ route('new.setup.settings') }}" enctype="multipart/form-data" class="grid gap-4">
                            @csrf

                            <x-form.field
                                label="Institution Name"
                                name="institution_name"
                                required
                                :value="$siteSettings->get('institution_name')"
                            />

                            <label class="form-control w-full">
                                <span class="label-text mb-2">Timezone</span>
                                <select name="timezone" class="select select-bordered w-full @error('timezone') select-error @enderror" required>
                                    @foreach (timezone_identifiers_list() as $tz)
                                        <option value="{{ $tz }}" @selected(old('timezone', $siteSettings->get('timezone', 'Asia/Manila')) === $tz)>
                                            {{ $tz }} (UTC{{ (new DateTimeZone($tz))->getOffset(new DateTime) >= 0 ? '+' : '' }}{{ gmdate('H:i', abs((new DateTimeZone($tz))->getOffset(new DateTime))) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('timezone') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </label>

                            <x-form.field
                                label="Institution Logo"
                                name="institution_logo"
                                type="file"
                                required
                                accept="image/*"
                                hint="Expected to be 1:1 aspect ratio."
                            />

                            <x-form.field
                                label="Institution Banner"
                                name="landing_banner"
                                type="file"
                                required
                                accept="image/*"
                            />

                            <x-form.field
                                label="Mission Statement (optional)"
                                name="mission"
                                :value="$siteSettings->get('mission')"
                            />

                            <x-form.field
                                label="Vision Statement (optional)"
                                name="vision"
                                :value="$siteSettings->get('vision')"
                            />

                            <button type="submit" class="btn btn-primary rounded-md w-full md:w-auto">Finish Setup</button>
                        </form>
                    </div>
                @endif
            </div>
        </section>
    </main>
</x-layouts.app>
