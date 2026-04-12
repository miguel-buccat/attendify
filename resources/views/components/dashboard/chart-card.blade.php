@props(['title', 'chartType', 'chartData', 'canvasId'])

<div class="rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden transition-all duration-300 hover:shadow-lg h-full flex flex-col">
    <div class="px-5 pt-5 pb-4 border-b border-base-300/30 flex items-center justify-between shrink-0">
        <h3 class="font-semibold text-sm">{{ $title }}</h3>
        <div class="flex items-center gap-1">
            <div class="size-1.5 rounded-full bg-primary/40"></div>
            <div class="size-1.5 rounded-full bg-primary/20"></div>
        </div>
    </div>
    <div class="p-5 flex-1">
        <div class="h-56 sm:h-64" data-chart="{{ $chartType }}" data-chart-data="{{ is_string($chartData) ? $chartData : json_encode($chartData) }}">
            <canvas id="{{ $canvasId }}"></canvas>
        </div>
    </div>
</div>
