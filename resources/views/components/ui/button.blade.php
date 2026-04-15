@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => false,
    'href' => null,
    'type' => 'button',
])

@php
    $variantClass = match ($variant) {
        'primary'   => 'af-btn-primary',
        'secondary' => 'af-btn-secondary',
        'ghost'     => 'af-btn-ghost',
        'outline'   => 'af-btn-outline',
        'danger'    => 'af-btn-danger',
        'warning'   => 'af-btn-warning',
        'success'   => 'af-btn-success',
        default     => 'af-btn-primary',
    };

    $sizeClass = match ($size) {
        'xs' => 'af-btn-xs',
        'sm' => 'af-btn-sm',
        'lg' => 'px-6 py-3 text-base',
        default => '',
    };

    $iconClass = $icon ? 'af-btn-icon' : '';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "af-btn {$variantClass} {$sizeClass} {$iconClass}"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "af-btn {$variantClass} {$sizeClass} {$iconClass}"]) }}>
        {{ $slot }}
    </button>
@endif
