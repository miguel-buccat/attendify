@props(['title' => null, 'maxWidth' => 'md', 'id'])

@php
    $widthClass = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        default => 'max-w-md',
    };
@endphp

<dialog id="{{ $id }}" class="modal">
    <div class="af-modal-box modal-box w-full {{ $widthClass }} rounded-2xl p-0 overflow-hidden border border-base-300/30 shadow-2xl">
        @if ($title || isset($modalHeader))
            <div class="flex items-center justify-between px-5 py-4 border-b af-divider">
                <div class="flex items-center gap-2 min-w-0">
                    @if (isset($modalHeader))
                        {{ $modalHeader }}
                    @else
                        <h3 class="font-bold text-base tracking-tight">{{ $title }}</h3>
                    @endif
                </div>
                <form method="dialog">
                    <button class="af-btn af-btn-ghost af-btn-icon af-btn-sm rounded-xl text-base-content/40 hover:text-base-content/70">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </form>
            </div>
        @endif

        <div class="p-5">
            {{ $slot }}
        </div>

        @if (isset($footer))
            <div class="px-5 py-4 border-t af-divider flex items-center justify-end gap-2">
                {{ $footer }}
            </div>
        @endif
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
