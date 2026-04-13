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

        {{-- Toast notification container --}}
        <div id="toast-container" class="fixed top-4 right-4 z-[300] flex flex-col gap-2 pointer-events-none max-w-sm w-full"></div>

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

            // Toast notification system
            function showToast(title, body, url) {
                var container = document.getElementById('toast-container');
                if (!container) return;

                var toast = document.createElement('div');
                toast.className = 'pointer-events-auto bg-base-100 border border-base-300/50 shadow-xl rounded-2xl px-4 py-3 flex items-start gap-3 cursor-pointer transform translate-x-full transition-transform duration-300 ease-out';
                toast.innerHTML = '<div class="shrink-0 mt-0.5"><span class="flex size-2 rounded-full bg-primary"></span></div>' +
                    '<div class="flex-1 min-w-0"><p class="text-sm font-semibold truncate">' + title + '</p>' +
                    '<p class="text-xs text-base-content/60 truncate">' + body + '</p></div>' +
                    '<button onclick="event.stopPropagation();this.parentElement.remove()" class="shrink-0 text-base-content/30 hover:text-base-content/60">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>';

                if (url && url !== '#') {
                    toast.addEventListener('click', function () { window.location.href = url; });
                }

                container.appendChild(toast);
                requestAnimationFrame(function () {
                    toast.classList.remove('translate-x-full');
                    toast.classList.add('translate-x-0');
                });

                setTimeout(function () {
                    toast.classList.remove('translate-x-0');
                    toast.classList.add('translate-x-full');
                    setTimeout(function () { toast.remove(); }, 300);
                }, 5000);
            }

            // Notification polling (auth'd pages only)
            @auth
            (function () {
                var lastIds = new Set();
                var firstPoll = true;

                function pollNotifications() {
                    fetch('{{ route("notifications.unread") }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        // Update badge count
                        var badges = document.querySelectorAll('#sidebar-notif-badge, #mobile-notif-badge');
                        badges.forEach(function (badge) {
                            if (data.count > 0) {
                                badge.textContent = data.count > 99 ? '99+' : data.count;
                                badge.classList.remove('hidden');
                            } else {
                                badge.classList.add('hidden');
                            }
                        });

                        // Show toasts for new notifications (skip first poll)
                        if (!firstPoll) {
                            data.notifications.forEach(function (n) {
                                if (!lastIds.has(n.id)) {
                                    showToast(n.title, n.body, n.url);
                                }
                            });
                        }

                        lastIds = new Set(data.notifications.map(function (n) { return n.id; }));
                        firstPoll = false;
                    })
                    .catch(function () {});
                }

                pollNotifications();
                setInterval(pollNotifications, 15000);
            })();
            @endauth
        </script>
    </body>
</html>
