@props(['name'])

@php
    $initials = strtoupper(substr($name, 0, 2));
@endphp

<div 
    class="h-9 w-9 rounded-full bg-surface-muted flex items-center justify-center border border-surface-border text-content-base text-xs font-bold shadow-inner"
    aria-hidden="true"
>
    {{ $initials }}
</div>