@props(['icon' => null, 'title', 'description' => null])

<div class="flex flex-col items-center gap-4 py-12 px-6 text-center">
    @if ($icon)
        <div class="size-16 rounded-2xl bg-base-200/80 border border-base-300/30 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-7 text-base-content/25">{!! $icon !!}</svg>
        </div>
    @endif
    <div class="space-y-1.5">
        <p class="font-semibold text-sm text-base-content/50">{{ $title }}</p>
        @if ($description)
            <p class="text-sm text-base-content/35 max-w-xs mx-auto">{{ $description }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="mt-1">{{ $slot }}</div>
    @endif
</div>
