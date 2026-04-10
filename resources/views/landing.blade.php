<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="valentine">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Attendify') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-base-200">
        <main class="min-h-screen grid place-items-center p-6">
            <div class="card bg-base-100 shadow-xl w-full max-w-xl">
                <div class="card-body items-center text-center gap-4">
                    <h1 class="card-title text-4xl font-bold">Hello World</h1>
                    <p class="text-base-content/70">Attendify is running with Tailwind CSS and DaisyUI (Valentine theme).</p>
                </div>
            </div>
        </main>
    </body>
</html>
