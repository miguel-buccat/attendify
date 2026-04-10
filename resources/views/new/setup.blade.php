<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="valentine">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>System Setup - {{ config('app.name', 'Attendify') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-base-200 text-base-content">
        <main class="min-h-screen grid place-items-center p-6 md:p-10">
            <section class="w-full max-w-3xl card bg-base-100 shadow-xl border border-base-300/70">
                <div class="card-body gap-6">
                    <div class="space-y-2">
                        <h1 class="text-3xl font-bold">System Setup</h1>
                        <p class="text-base-content/70">Complete the 2 setup steps before using Attendify.</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success">
                            <span>{{ session('status') }}</span>
                        </div>
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

                                <label class="form-control w-full">
                                    <span class="label-text mb-2">Name</span>
                                    <input type="text" name="name" value="{{ old('name') }}" class="input input-bordered w-full" required>
                                    @error('name')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                                </label>

                                <label class="form-control w-full">
                                    <span class="label-text mb-2">Email</span>
                                    <input type="email" name="email" value="{{ old('email') }}" class="input input-bordered w-full" required>
                                    @error('email')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                                </label>

                                <label class="form-control w-full">
                                    <span class="label-text mb-2">Password</span>
                                    <input type="password" name="password" class="input input-bordered w-full" required>
                                    @error('password')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                                </label>

                                <label class="form-control w-full">
                                    <span class="label-text mb-2">Confirm Password</span>
                                    <input type="password" name="password_confirmation" class="input input-bordered w-full" required>
                                </label>

                                <button type="submit" class="btn btn-primary rounded-md w-full md:w-auto">Create Admin Account</button>
                            </form>
                        </div>
                    @else
                        <div class="space-y-4">
                            <h2 class="text-xl font-semibold">Step 2: Site Settings</h2>

                            <form method="POST" action="{{ route('new.setup.settings') }}" enctype="multipart/form-data" class="grid gap-4">
                                @csrf

                                <label class="form-control w-full">
                                    <span class="label-text mb-2">Institution Name</span>
                                    <input
                                        type="text"
                                        name="institution_name"
                                        value="{{ old('institution_name', $siteSettings->get('institution_name')) }}"
                                        class="input input-bordered w-full"
                                        required
                                    >
                                    @error('institution_name')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                                </label>

                                <label class="form-control w-full">
                                    <span class="label-text mb-2">Institution Logo</span>
                                    <input type="file" name="institution_logo" accept="image/*" class="file-input file-input-bordered w-full" required>
                                    <span class="text-base-content/60 text-sm mt-1">Expected to be 1:1 aspect ratio.</span>
                                    @error('institution_logo')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                                </label>

                                <label class="form-control w-full">
                                    <span class="label-text mb-2">Institution Banner</span>
                                    <input type="file" name="landing_banner" accept="image/*" class="file-input file-input-bordered w-full" required>
                                    @error('landing_banner')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                                </label>

                                <button type="submit" class="btn btn-primary rounded-md w-full md:w-auto">Finish Setup</button>
                            </form>
                        </div>
                    @endif
                </div>
            </section>
        </main>
    </body>
</html>
