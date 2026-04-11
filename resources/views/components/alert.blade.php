@props(['type' => 'success'])

<div class="alert alert-{{ $type }}">
    <span>{{ $slot }}</span>
</div>
