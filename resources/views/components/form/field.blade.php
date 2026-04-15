@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'autofocus' => false,
    'hint' => null,
])

@php $fieldId = 'field-' . $name . '-' . md5(uniqid()); @endphp

<div class="w-full space-y-1.5">
    @if ($label)
        <label for="{{ $fieldId }}" class="block text-[13px] font-semibold text-base-content/70">{{ $label }} @if ($required)<span class="text-error/70">*</span>@endif</label>
    @endif

    @if ($type === 'file')
        <input
            type="file"
            id="{{ $fieldId }}"
            name="{{ $name }}"
            class="af-input file:mr-3 file:border-0 file:bg-primary/10 file:text-primary file:text-sm file:font-medium file:rounded-lg file:px-3 file:py-1.5 file:cursor-pointer @error($name) af-input-error @enderror"
            @if ($required) required @endif
            {{ $attributes }}
        >
    @elseif ($type === 'password')
        <div class="relative">
            <input
                type="password"
                id="{{ $fieldId }}"
                name="{{ $name }}"
                class="af-input pr-10 @error($name) af-input-error @enderror"
                @if ($required) required @endif
                @if ($autofocus) autofocus @endif
                {{ $attributes }}
            >
            <button
                type="button"
                onclick="(function(btn){var inp=document.getElementById('{{ $fieldId }}');var isHidden=inp.type==='password';inp.type=isHidden?'text':'password';btn.querySelector('.eye-show').classList.toggle('hidden',isHidden);btn.querySelector('.eye-hide').classList.toggle('hidden',!isHidden);})(this)"
                class="absolute inset-y-0 right-0 flex items-center px-3 text-base-content/30 hover:text-base-content/60 transition-colors"
                aria-label="Toggle password visibility"
                tabindex="-1"
            >
                <svg class="eye-show size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M2 12s3.636-7 10-7 10 7 10 7-3.636 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
                </svg>
                <svg class="eye-hide size-4 hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-6.364 0-10-7-10-7a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c6.364 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 2l20 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    @else
        <input
            type="{{ $type }}"
            id="{{ $fieldId }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="af-input @error($name) af-input-error @enderror"
            @if ($required) required @endif
            @if ($autofocus) autofocus @endif
            {{ $attributes }}
        >
    @endif

    @if ($hint)
        <p class="text-base-content/45 text-xs mt-1">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-error text-xs mt-1 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            {{ $message }}
        </p>
    @enderror
</div>
