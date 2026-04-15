@props(['title' => null, 'subtitle' => null, 'hover' => false, 'padding' => true, 'href' => null])

@php
    $classes = 'af-card ' . ($hover ? 'af-card-hover cursor-pointer' : '') . ' overflow-hidden';
    $bodyClass = $padding ? 'p-5' : '';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "block {$classes}"]) }}>
@else
    <div {{ $attributes->merge(['class' => $classes]) }}>
@endif

    @if ($title || isset($header))
        <div class="px-5 py-4 border-b af-divider flex items-center justify-between gap-3">
            <div class="min-w-0">
                @if ($title)
                    <h3 class="font-semibold text-sm">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs text-base-content/40 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if (isset($header))
                {{ $header }}
            @endif
        </div>
    @endif

    <div class="{{ $bodyClass }}">
        {{ $slot }}
    </div>

@if ($href)
    </a>
@else
    </div>
@endif
