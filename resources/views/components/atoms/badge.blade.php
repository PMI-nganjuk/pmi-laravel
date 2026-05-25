@props([
    'variant' => 'neutral',
])

@php
    $variants = [
        'neutral' => 'bg-surface-muted text-content-muted border-surface-border',
        'error'   => 'bg-state-error/10 text-state-error border-state-error/20',
        'primary' => 'bg-primary/10 text-primary border-primary/20',
        'success' => 'bg-success-bg text-success-text border-success-border',
        'warning' => 'bg-warning-bg text-warning-text border-warning-border',
        'danger'  => 'bg-danger-bg text-danger-text border-danger-border',
        'info'    => 'bg-info-bg text-info-text border-info-border',
        'accent'  => 'bg-accent-bg text-accent-text border-accent-border',
    ];

    $style = $variants[$variant] ?? $variants['neutral'];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $style }}">
    {{ $slot }}
</span>
