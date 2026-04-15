@props(['type' => 'success', 'message' => null])

@php
    $variant = match ($type) {
        'success' => 'success',
        'error', 'danger' => 'error',
        'warning' => 'warning',
        default => 'info',
    };
@endphp

<x-ui.alert :variant="$variant">{{ $message ?? $slot }}</x-ui.alert>
