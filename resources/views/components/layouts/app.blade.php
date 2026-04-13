@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? $title . ' – ' . config('app.name', 'Attendify') : config('app.name', 'Attendify') }}</title>

        <link rel="icon" type="image/png" sizes="32x32" href="/assets/attendify.png">

        <script>
            (function () {
                var t = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', t);
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-base-200 text-base-content">
        {{ $slot }}

        <script>
            // Global fallback – sidebar will override this on auth'd pages
            if (typeof toggleTheme === 'undefined') {
                function toggleTheme() {
                    var t = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-theme', t);
                    localStorage.setItem('theme', t);
                    if (typeof syncThemeIcons === 'function') syncThemeIcons(t);
                }
            }
        </script>
    </body>
</html>
