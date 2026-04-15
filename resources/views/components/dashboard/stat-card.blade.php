@props(['icon' => null, 'label', 'value', 'color' => 'primary', 'href' => null])

@php
    $raw = (string) $value;
    $numericStr = preg_replace('/[^0-9.]/', '', $raw);
    $numericSuffix = $numericStr !== '' ? str_replace($numericStr, '', $raw) : '';

    $textColor = match ($color) {
        'success'   => 'text-success',
        'warning'   => 'text-warning',
        'error'     => 'text-error',
        'info'      => 'text-info',
        'secondary' => 'text-secondary',
        'accent'    => 'text-accent',
        default     => 'text-primary',
    };
    $iconBg = match ($color) {
        'success'   => 'bg-success/15 ring-success/20',
        'warning'   => 'bg-warning/15 ring-warning/20',
        'error'     => 'bg-error/15 ring-error/20',
        'info'      => 'bg-info/15 ring-info/20',
        'secondary' => 'bg-secondary/15 ring-secondary/20',
        'accent'    => 'bg-accent/15 ring-accent/20',
        default     => 'bg-primary/15 ring-primary/20',
    };
    $glowColor = match ($color) {
        'success'   => 'from-success/10',
        'warning'   => 'from-warning/10',
        'error'     => 'from-error/10',
        'info'      => 'from-info/10',
        'secondary' => 'from-secondary/10',
        'accent'    => 'from-accent/10',
        default     => 'from-primary/10',
    };
    $borderColor = match ($color) {
        'success'   => 'border-success/25',
        'warning'   => 'border-warning/25',
        'error'     => 'border-error/25',
        'info'      => 'border-info/25',
        'secondary' => 'border-secondary/25',
        'accent'    => 'border-accent/25',
        default     => 'border-primary/25',
    };
    $leftBorder = match ($color) {
        'success'   => 'border-l-success',
        'warning'   => 'border-l-warning',
        'error'     => 'border-l-error',
        'info'      => 'border-l-info',
        'secondary' => 'border-l-secondary',
        'accent'    => 'border-l-accent',
        default     => 'border-l-primary',
    };
    $glowShadow = match ($color) {
        'success'   => 'hover:shadow-success/15',
        'warning'   => 'hover:shadow-warning/15',
        'error'     => 'hover:shadow-error/15',
        'info'      => 'hover:shadow-info/15',
        'secondary' => 'hover:shadow-secondary/15',
        'accent'    => 'hover:shadow-accent/15',
        default     => 'hover:shadow-primary/15',
    };
@endphp

@php $classes = "group relative overflow-hidden rounded-2xl border border-l-[3px] {$borderColor} {$leftBorder} bg-base-100 p-5 transition-all duration-200 hover:-translate-y-1 hover:shadow-xl {$glowShadow}"; @endphp

@if ($href)
    <a href="{{ $href }}" class="block {{ $classes }}">
@else
    <article class="{{ $classes }}">
@endif
    {{-- Gradient glow --}}
    <div class="absolute inset-0 bg-gradient-to-br {{ $glowColor }} to-transparent pointer-events-none opacity-60"></div>
    {{-- Ghost icon --}}
    @if ($icon)
        <div class="absolute -right-3 -bottom-3 pointer-events-none {{ $textColor }}" style="opacity:.05" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-24">{!! $icon !!}</svg>
        </div>
    @endif
    <div class="relative z-10 flex flex-col gap-3">
        @if ($icon)
            <div class="inline-flex items-center justify-center size-10 rounded-xl {{ $iconBg }} ring-1 shadow-md shadow-current/10">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 {{ $textColor }} drop-shadow-[0_0_4px_currentColor]" style="filter: drop-shadow(0 0 4px currentColor) drop-shadow(0 0 1px currentColor);" aria-hidden="true">
                    {!! $icon !!}
                </svg>
            </div>
        @endif
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[.18em] text-base-content/40">{{ $label }}</p>
            <p class="mt-1 text-3xl font-black tracking-tight {{ $textColor }} tabular-nums"
               @if ($numericStr !== '') data-count="{{ $numericStr }}" data-suffix="{{ $numericSuffix }}" @endif>{{ $value }}</p>
        </div>
    </div>
@if ($href)
    </a>
@else
    </article>
@endif
