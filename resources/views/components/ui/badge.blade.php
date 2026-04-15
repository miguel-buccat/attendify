@props(['variant' => 'neutral', 'size' => 'sm', 'dot' => false])

@php
    $pillClass = match ($variant) {
        'primary'   => 'af-pill-primary',
        'secondary' => 'af-pill-secondary',
        'accent'    => 'af-pill-accent',
        'success'   => 'af-pill-success',
        'warning'   => 'af-pill-warning',
        'error'     => 'af-pill-error',
        'info'      => 'af-pill-info',
        default     => 'af-pill-neutral',
    };

    $sizeClass = match ($size) {
        'xs' => 'text-[10px] px-2 py-0.5',
        'lg' => 'text-sm px-3.5 py-1.5',
        default => 'text-xs px-2.5 py-0.5',
    };
@endphp

<span {{ $attributes->merge(['class' => "af-pill {$pillClass} {$sizeClass}"]) }}>
    @if ($dot)
        <span class="size-1.5 rounded-full bg-current opacity-80"></span>
    @endif
    {{ $slot }}
</span>
