@props(['eyebrow' => null, 'title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => '']) }}>
    @if ($eyebrow)
        <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">{{ $eyebrow }}</p>
    @endif
    <h1 class="text-2xl md:text-3xl font-black tracking-tight">{{ $title }}</h1>
    @if ($subtitle)
        <p class="mt-1 text-sm text-base-content/50">{{ $subtitle }}</p>
    @endif
</div>
