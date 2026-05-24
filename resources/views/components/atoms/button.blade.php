@props([
    'as' => 'button',
    'type' => 'button',
    'variant' => 'secondary',
    'size' => 'sm',
    'href' => null,
    'disabled' => false,
    'loading' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-offset-surface-base disabled:opacity-50 disabled:cursor-not-allowed';
    
    $sizes = [
        'sm' => 'gap-1.5 py-1.5 px-3 rounded-lg text-xs',
        'md' => 'gap-2 py-2.5 px-4 rounded-xl text-sm',
    ];

    $variants = [
        'primary'   => 'bg-primary text-content-inverse hover:bg-primary-hover focus-visible:ring-primary',
        'secondary' => 'bg-surface-muted border border-surface-border text-content-base hover:bg-surface-hover focus-visible:ring-surface-border',
        'danger'    => 'bg-danger-bg border border-danger-border text-danger-text hover:bg-danger-hover focus-visible:ring-danger',
        'outline'   => 'bg-surface-base border border-surface-border text-content-base hover:bg-surface-hover focus-visible:ring-surface-border',
        'info'      => 'bg-info text-content-inverse hover:bg-info-hover focus-visible:ring-info',
    ];

    $classes = $baseClasses . ' ' . ($sizes[$size] ?? $sizes['sm']) . ' ' . ($variants[$variant] ?? $variants['secondary']);

    if (($disabled || $loading) && $as === 'a') {
        $classes .= ' opacity-50 cursor-not-allowed pointer-events-none';
    }
@endphp

@if($as === 'a' && $href)
    <a 
        href="{{ $href }}" 
        aria-busy="{{ $loading ? 'true' : 'false' }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        {{-- Loading Spinner SVG --}}
        @if($loading)
            <svg class="animate-spin h-4 w-4 text-current" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif

        {{ $slot }}
    </a>
@else
    <button 
        type="{{ $type }}"
        {{-- Tombol mati saat disabled ATAU sedang loading agar user tidak double-submit --}}
        {{ $disabled || $loading ? 'disabled' : '' }}
        aria-busy="{{ $loading ? 'true' : 'false' }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        @if($loading)
            <svg class="animate-spin h-4 w-4 text-current" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif

        {{ $slot }}
    </button>
@endif
