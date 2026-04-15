@props(['title', 'chartType', 'chartData', 'canvasId'])

<div class="af-card overflow-hidden h-full flex flex-col">
    <div class="px-5 pt-5 pb-4 border-b af-divider flex items-center justify-between shrink-0">
        <h3 class="font-semibold text-sm">{{ $title }}</h3>
        <div class="flex items-center gap-1.5">
            <div class="size-2 rounded-full bg-success af-pulse shadow-sm shadow-success/40"></div>
            <span class="text-[10px] font-medium text-base-content/30 uppercase tracking-wider">Live</span>
        </div>
    </div>
    <div class="p-5 flex-1">
        <div class="h-56 sm:h-64" data-chart="{{ $chartType }}" data-chart-data="{{ is_string($chartData) ? $chartData : json_encode($chartData) }}">
            <canvas id="{{ $canvasId }}"></canvas>
        </div>
    </div>
</div>
