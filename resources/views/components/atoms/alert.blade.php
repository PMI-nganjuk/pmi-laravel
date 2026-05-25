@props([
    'variant' => 'default',
    'title' => null,
])

@php
    $variants = [
        'success' => 'border-success-border bg-success-bg text-success-text',
        'danger' => 'border-danger-border bg-danger-bg text-danger-text',
        'info' => 'border-info-border bg-info-bg text-info-text',
    ];

    $iconName = match($variant) {
        'success' => 'check-circle',
        'danger' => 'exclamation-circle',
        'info' => 'info-circle',
        default => 'info-circle',
    };

    $classes = 'flex items-start gap-3 rounded-xl border px-4 py-3 text-sm shadow-sm ' . ($variants[$variant] ?? $variants['default']);
@endphp

<div class="{{ $classes }}" role="alert">
    <x-atoms.icon :name="$iconName" class="h-5 w-5 shrink-0 flex-none" />

    <div class="flex-1">
        @if($title)
            <p class="font-bold">{{ $title }}</p>
        @endif

        <div @class(['mt-1' => $title])>
            {{ $slot }}
        </div>
    </div>
</div>
