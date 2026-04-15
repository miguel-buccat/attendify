@props(['label' => null, 'count' => null, 'action' => null, 'actionLabel' => null, 'actionHref' => null])

<div class="px-5 py-4 border-b af-divider flex items-center justify-between gap-3">
    <div class="flex items-center gap-3 min-w-0">
        <h2 class="font-semibold text-sm">{{ $label }}</h2>
        @if ($count !== null)
            <span class="text-xs text-base-content/40 tabular-nums">{{ $count }}</span>
        @endif
    </div>
    @if ($actionHref)
        <a href="{{ $actionHref }}" class="text-xs font-semibold text-primary hover:text-primary/80 transition-all hover:drop-shadow-[0_0_6px_oklch(from_var(--color-primary)_l_c_h_/_0.4)]">{{ $actionLabel ?? 'View all' }} →</a>
    @elseif ($action)
        {{ $action }}
    @endif
</div>
