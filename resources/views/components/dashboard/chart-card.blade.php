@props(['title', 'chartType', 'chartData', 'canvasId'])

<div class="rounded-2xl border border-base-300/60 bg-base-100 p-5 space-y-4 transition-all duration-200 hover:shadow-md">
    <h3 class="font-semibold">{{ $title }}</h3>
    <div class="h-56 sm:h-64" data-chart="{{ $chartType }}" data-chart-data="{{ is_string($chartData) ? $chartData : json_encode($chartData) }}">
        <canvas id="{{ $canvasId }}"></canvas>
    </div>
</div>
