@props(['variant' => 'info', 'dismissible' => true])

@php
    $styles = match ($variant) {
        'success' => 'bg-success/8 border-success/20 text-success',
        'warning' => 'bg-warning/8 border-warning/20 text-warning',
        'error'   => 'bg-error/8 border-error/20 text-error',
        default   => 'bg-info/8 border-info/20 text-info',
    };
    $icon = match ($variant) {
        'success' => '<path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
        'warning' => '<path d="M10.29 3.86L1.71 18a2 2 0 0 0 1.71 3h16.58a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0ZM12 9v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'error'   => '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M15 9l-6 6m0-6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        default   => '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 16v-4m0-4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
    };
@endphp

<div {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-xl border px-4 py-3 text-sm {$styles}"]) }}>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 shrink-0 mt-0.5">{!! $icon !!}</svg>
    <div class="flex-1 min-w-0 font-medium">{{ $slot }}</div>
    @if ($dismissible)
        <button type="button" onclick="this.closest('[class*=flex]').remove()" class="shrink-0 opacity-50 hover:opacity-100 transition-opacity mt-0.5">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
    @endif
</div>
