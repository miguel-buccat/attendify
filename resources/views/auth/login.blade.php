<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="valentine">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Login - {{ config('app.name', 'Attendify') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-base-200 text-base-content">
        @php
            $siteSettings = app(\App\Support\SiteSettings::class);
            $institutionName = $siteSettings->get('institution_name', 'Attendify');
            $institutionLogo = $siteSettings->get('institution_logo') ?: asset('assets/attendify.png');
            $landingBanner = $siteSettings->get('landing_banner');
        @endphp

        <main class="relative min-h-screen grid place-items-center p-6 overflow-hidden">
            @if ($landingBanner)
                <img
                    src="{{ $landingBanner }}"
                    alt="Institution banner"
                    class="absolute inset-0 h-full w-full object-cover opacity-20"
                >
            @endif

            <div class="absolute inset-0 bg-gradient-to-br from-base-300/70 via-base-200/80 to-base-100/80"></div>

            <section class="card relative z-10 w-full max-w-md bg-base-100/95 backdrop-blur-sm shadow-2xl border border-base-300/80">
                <div class="card-body gap-5">
                    <div class="flex flex-col items-center gap-3 text-center">
                        <div class="size-20 rounded-2xl bg-base-200 border border-base-300 p-3 shadow-sm">
                            <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" class="h-full w-full object-contain">
                        </div>
                        <div>
                            <p class="text-sm uppercase tracking-wider text-base-content/60">{{ $institutionName }}</p>
                            <h1 class="text-2xl font-semibold">Login</h1>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success">
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}" class="grid gap-4">
                        @csrf

                        <label class="form-control w-full">
                            <span class="label-text mb-2">Email</span>
                            <input type="email" name="email" value="{{ old('email') }}" class="input input-bordered w-full" required autofocus>
                            @error('email')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                        </label>

                        <label class="form-control w-full">
                            <span class="label-text mb-2">Password</span>
                            <input type="password" name="password" class="input input-bordered w-full" required>
                            @error('password')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                        </label>

                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="remember" class="checkbox checkbox-sm">
                            <span class="label-text">Remember me</span>
                        </label>

                        <button type="submit" class="btn btn-primary rounded-md">Login</button>
                    </form>

                    <a href="{{ route('password.request') }}" class="link link-hover">Forgot password?</a>
                </div>
            </section>
        </main>
    </body>
</html>
