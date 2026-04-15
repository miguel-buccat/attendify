<x-layouts.app title="Activity Log">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="activity-log" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-5">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-black tracking-tight">Activity Log</h1>
                        <p class="text-sm text-base-content/50 mt-1">System-wide audit trail of actions</p>
                    </div>
                </div>

                {{-- Filters --}}
                <form method="GET" action="{{ route('admin.activity-log.index') }}" class="flex flex-wrap gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search descriptions..."
                        class="af-input w-64" />
                    <select name="action" class="af-input">
                        <option value="">All Actions</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>{{ str_replace('_', ' ', ucfirst($action)) }}</option>
                        @endforeach
                    </select>
                    <x-ui.button type="submit" variant="primary" size="sm">Filter</x-ui.button>
                    @if (request()->hasAny(['search', 'action', 'user']))
                        <x-ui.button href="{{ route('admin.activity-log.index') }}" variant="ghost" size="sm">Clear</x-ui.button>
                    @endif
                </form>

                {{-- Log Table --}}
                <div class="af-card overflow-hidden !p-0">
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr class="border-b af-divider">
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">When</th>
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">User</th>
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">Action</th>
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">Description</th>
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr class="border-b af-divider hover:bg-base-content/[.03]">
                                        <td class="text-xs text-base-content/60 whitespace-nowrap">
                                            {{ $log->created_at->format('M d, Y') }}<br>
                                            <span class="text-base-content/40">{{ $log->created_at->format('g:i A') }}</span>
                                        </td>
                                        <td>
                                            @if ($log->user)
                                                <div class="flex items-center gap-2">
                                                    @if ($log->user->avatar_url)
                                                        <img src="{{ $log->user->avatar_url }}" class="size-6 rounded-lg object-cover" alt="">
                                                    @else
                                                        <span class="inline-flex items-center justify-center size-6 rounded-lg bg-primary/15 text-primary text-[10px] font-bold">
                                                            {{ mb_strtoupper(mb_substr($log->user->name, 0, 1)) }}
                                                        </span>
                                                    @endif
                                                    <span class="text-sm font-medium">{{ $log->user->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-xs text-base-content/40">System</span>
                                            @endif
                                        </td>
                                        <td>
                                            <x-ui.badge variant="neutral" size="xs">{{ str_replace('_', ' ', $log->action) }}</x-ui.badge>
                                        </td>
                                        <td class="text-sm max-w-xs truncate">{{ $log->description }}</td>
                                        <td class="text-xs text-base-content/40 font-mono">{{ $log->ip_address }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-12 text-base-content/40">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-8 mx-auto mb-2 opacity-30"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8"/><path d="M14 2v6h6" stroke="currentColor" stroke-width="1.8"/></svg>
                                            No activity logged yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($logs->hasPages())
                        <div class="p-4 border-t af-divider">
                            {{ $logs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
