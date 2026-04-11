@props(['title', 'chartType', 'chartData', 'canvasId'])

<div class="rounded-xl border border-base-300 bg-base-100 p-4 space-y-3">
    <h3 class="font-semibold text-sm">{{ $title }}</h3>
    <div class="h-56 sm:h-64" data-chart="{{ $chartType }}" data-chart-data="{{ is_string($chartData) ? $chartData : json_encode($chartData) }}">
        <canvas id="{{ $canvasId }}"></canvas>
    </div>
</div>
