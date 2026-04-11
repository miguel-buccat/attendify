@props(['icon' => null, 'label', 'value', 'color' => 'primary', 'href' => null])

@php
    $colorClass = match ($color) {
        'success' => 'text-success',
        'warning' => 'text-warning',
        'error' => 'text-error',
        'info' => 'text-info',
        'secondary' => 'text-secondary',
        'accent' => 'text-accent',
        default => 'text-primary',
    };

    $bgClass = match ($color) {
        'success' => 'bg-success/10',
        'warning' => 'bg-warning/10',
        'error' => 'bg-error/10',
        'info' => 'bg-info/10',
        'secondary' => 'bg-secondary/10',
        'accent' => 'bg-accent/10',
        default => 'bg-primary/10',
    };

    $tag = $href ? 'a' : 'article';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif class="rounded-2xl border border-base-300/60 bg-base-100 p-4 sm:p-5 flex items-start gap-3 transition-all duration-200 hover:shadow-md hover:border-base-300">
    @if ($icon)
        <div class="shrink-0 flex items-center justify-center size-11 rounded-xl {{ $bgClass }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 {{ $colorClass }}" aria-hidden="true">
                {!! $icon !!}
            </svg>
        </div>
    @endif
    <div class="min-w-0">
        <p class="text-xs uppercase tracking-wider text-base-content/50 font-medium">{{ $label }}</p>
        <p class="mt-1 text-2xl sm:text-3xl font-bold {{ $colorClass }}">{{ $value }}</p>
    </div>
</{{ $tag }}>
