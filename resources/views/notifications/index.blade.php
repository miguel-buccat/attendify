<x-layouts.app title="Notifications">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="notifications" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-5">

                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-black tracking-tight">Notifications</h1>
                        <p class="text-sm text-base-content/50 mt-1">Stay updated on your activity</p>
                    </div>
                    @if ($notifications->where('read_at', null)->count() > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <x-ui.button type="submit" variant="ghost" size="sm">Mark all as read</x-ui.button>
                        </form>
                    @endif
                </div>

                <div class="af-card !p-0 overflow-hidden divide-y divide-base-content/6">
                    @forelse ($notifications as $notification)
                        <!-- <a href="{{ $notification->data['url'] ?? '#' }}" -->
                        <a
                           onclick="fetch('{{ route('notifications.read', $notification->id) }}', {method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}})"
                           class="flex items-start gap-3 px-5 py-4 hover:bg-base-content/[.03] transition-colors {{ $notification->read_at ? 'opacity-60' : '' }}">
                            <div class="mt-0.5 shrink-0">
                                @if (! $notification->read_at)
                                    <span class="size-2.5 rounded-full bg-primary block"></span>
                                @else
                                    <span class="size-2.5 block"></span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold {{ $notification->read_at ? '' : 'text-base-content' }}">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </p>
                                <p class="text-sm text-base-content/60 truncate">{{ $notification->data['body'] ?? '' }}</p>
                                <p class="text-xs text-base-content/35 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="py-12 text-center text-sm text-base-content/40">No notifications yet.</div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
