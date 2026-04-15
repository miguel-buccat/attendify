@props(['label' => null, 'count' => null, 'action' => null, 'actionLabel' => null, 'actionHref' => null])

<div class="px-5 py-4 border-b af-divider flex items-center justify-between gap-3">
    <div class="flex items-center gap-3 min-w-0">
        <h2 class="font-semibold text-sm">{{ $label }}</h2>
        @if ($count !== null)
            <span class="text-xs text-base-content/40 tabular-nums">{{ $count }}</span>
        @endif
    </div>
    @if ($actionHref)
        <a href="{{ $actionHref }}" class="text-xs font-medium text-primary hover:text-primary/80 transition-colors">{{ $actionLabel ?? 'View all' }} →</a>
    @elseif ($action)
        {{ $action }}
    @endif
</div>
