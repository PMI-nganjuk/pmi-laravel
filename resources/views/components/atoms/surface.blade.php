@props([
    'tag' => 'div',
    'variant' => 'default'
])

@php
    $baseClasses = 'border rounded-xl p-4 transition duration-200 sm:p-6';
    
    $variants = [
        'default' => 'bg-surface-base border-surface-border shadow-sm text-content-base',
        'subtle' => 'bg-surface-muted border-surface-border text-content-base',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['default']);
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $tag }}>
