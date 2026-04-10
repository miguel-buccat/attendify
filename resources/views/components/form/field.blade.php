@props([
    'label',
    'name',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'autofocus' => false,
    'hint' => null,
])

<label class="form-control w-full">
    <span class="label-text mb-2">{{ $label }}</span>

    @if ($type === 'file')
        <input
            type="file"
            name="{{ $name }}"
            class="file-input file-input-bordered w-full @error($name) file-input-error @enderror"
            @if ($required) required @endif
            {{ $attributes }}
        >
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
            class="input input-bordered w-full @error($name) input-error @enderror"
            @if ($required) required @endif
            @if ($autofocus) autofocus @endif
            {{ $attributes }}
        >
    @endif

    @if ($hint)
        <span class="text-base-content/60 text-sm mt-1">{{ $hint }}</span>
    @endif

    @error($name)
        <span class="text-error text-sm mt-1">{{ $message }}</span>
    @enderror
</label>
