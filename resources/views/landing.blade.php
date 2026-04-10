<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="valentine">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ config('app.name', 'Attendify') }}</title>

		@vite(['resources/css/app.css', 'resources/js/app.js'])
	</head>
	<body class="min-h-screen bg-base-200 text-base-content">
		@php
			$siteSettings = app(\App\Support\SiteSettings::class);
			$institutionName = $siteSettings->get('institution_name', 'Attendify');
			$institutionLogo = $siteSettings->get('institution_logo');
			$landingBanner = $siteSettings->get('landing_banner');
			$displayLogo = $institutionLogo ?: asset('assets/attendify.png');
			$displayBanner = $landingBanner ?: null;
		@endphp

		<main class="min-h-screen flex flex-col">
			<section class="relative flex-1 grid lg:grid-cols-2 items-center gap-10 px-6 py-10 md:px-12 lg:px-20 overflow-hidden">
				@if ($displayBanner)
					<img
						src="{{ $displayBanner }}"
						alt="Institution banner"
						class="absolute inset-0 h-full w-full object-cover opacity-10"
					>
				@endif

				<div class="hidden lg:flex items-center justify-center relative">
					<div class="w-full max-w-md xl:max-w-lg p-10">
						<img
							src="{{ $displayLogo }}"
							alt="{{ $institutionName }} logo"
							class="w-full h-full object-contain"
						>
					</div>
				</div>

				<div class="relative w-full max-w-2xl p-7 md:p-10 flex flex-col items-start gap-7">
					<h1 class="text-4xl md:text-6xl font-black leading-[1.04] tracking-tight">
						<span class="block">{{ $institutionName }}</span>
						<span class="block text-primary">Attendance System</span>
					</h1>

					<a href="/login" class="btn btn-primary btn-lg rounded-md px-8 shadow-lg shadow-primary/25 inline-flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true">
							<path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
						<span>Login to Account</span>
					</a>
				</div>
			</section>

			<footer class="relative border-t border-base-300/70 px-6 py-5 md:px-12 lg:px-20 text-sm text-base-content/70 text-center bg-base-100/70 backdrop-blur-sm">
				Attendify licensed under the MIT License. Copyright &copy; {{ now()->year }} Attendify Developers.
			</footer>
		</main>
	</body>
</html>
