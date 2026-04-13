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
        'success'   => 'bg-success/20',
        'warning'   => 'bg-warning/20',
        'error'     => 'bg-error/20',
        'info'      => 'bg-info/20',
        'secondary' => 'bg-secondary/20',
        'accent'    => 'bg-accent/20',
        default     => 'bg-primary/20',
    };
    $bgFrom = match ($color) {
        'success'   => 'from-success/15',
        'warning'   => 'from-warning/15',
        'error'     => 'from-error/15',
        'info'      => 'from-info/15',
        'secondary' => 'from-secondary/15',
        'accent'    => 'from-accent/15',
        default     => 'from-primary/15',
    };
    $borderColor = match ($color) {
        'success'   => 'border-success/35',
        'warning'   => 'border-warning/35',
        'error'     => 'border-error/35',
        'info'      => 'border-info/35',
        'secondary' => 'border-secondary/35',
        'accent'    => 'border-accent/35',
        default     => 'border-primary/35',
    };
@endphp

@php $classes = "group relative overflow-hidden rounded-2xl border {$borderColor} bg-base-100 p-5 sm:p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"; @endphp

@if ($href)
    <a href="{{ $href }}" class="block {{ $classes }}">
@else
    <article class="{{ $classes }}">
@endif
    {{-- gradient tint --}}
    <div class="absolute inset-0 bg-gradient-to-br {{ $bgFrom }} to-transparent pointer-events-none"></div>
    {{-- ghost icon overlay --}}
    @if ($icon)
        <div class="absolute -right-4 -bottom-4 pointer-events-none {{ $textColor }}" style="opacity:.055" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-28">{!! $icon !!}</svg>
        </div>
    @endif
    <div class="relative z-10 space-y-4">
        @if ($icon)
            <div class="inline-flex items-center justify-center size-10 rounded-xl {{ $iconBg }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 {{ $textColor }}" aria-hidden="true">
                    {!! $icon !!}
                </svg>
            </div>
        @endif
        <div>
            <p class="text-xs uppercase tracking-widest text-base-content/40 font-semibold">{{ $label }}</p>
            <p class="mt-1.5 text-3xl sm:text-4xl font-black tracking-tight {{ $textColor }} tabular-nums"
               @if ($numericStr !== '') data-count="{{ $numericStr }}" data-suffix="{{ $numericSuffix }}" @endif>{{ $value }}</p>
        </div>
    </div>
@if ($href)
    </a>
@else
    </article>
@endif
