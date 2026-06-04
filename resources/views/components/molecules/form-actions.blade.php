@props([
    'cancelText' => 'Batal',
    'submitText' => 'Simpan Data',
    'loadingText' => 'Menyimpan...',
    
    'cancelShow' => null,
    'cancelClick' => null,
    'submitDisabled' => null,
    'submitBusy' => null,
    'loadingShow' => null,
    'submitXText' => null,
    
    'cancelAriaLabel' => 'Batal dan tutup form',
    'submitAriaLabel' => 'Simpan perubahan formulir',
])

@php
    $cancelButtonAttributes = [];
    if ($cancelShow) $cancelButtonAttributes['x-show'] = $cancelShow;
    if ($cancelClick) $cancelButtonAttributes['x-on:click'] = $cancelClick;

    $submitButtonAttributes = [];
    if ($submitDisabled) $submitButtonAttributes['x-bind:disabled'] = $submitDisabled;
    if ($submitBusy) $submitButtonAttributes['x-bind:aria-busy'] = $submitBusy;
@endphp

<div {{ $attributes->merge(['class' => 'mt-2 flex flex-col-reverse gap-3 border-t border-surface-border pt-5 sm:flex-row sm:justify-end md:col-span-2']) }}>
    
    <x-atoms.button
        type="button"
        variant="secondary"
        size="md"
        aria-label="{{ $cancelAriaLabel }}"
        {{ $attributes->merge($cancelButtonAttributes) }}
    >
        {{ $cancelText }}
    </x-atoms.button>

    <x-atoms.button
        type="submit"
        variant="primary"
        size="md"
        aria-label="{{ $submitAriaLabel }}"
        {{ $attributes->merge($submitButtonAttributes) }}
    >
        @if($loadingShow)
            <span x-show="{{ $loadingShow }}" x-cloak>
                {{ $loadingText }}
            </span>
        @endif
        
        <span 
            @if($loadingShow) x-show="!({{ $loadingShow }})" @endif 
            @if($submitXText) x-text="{{ $submitXText }}" @endif
        >
            {{ $submitText }}
        </span>
    </x-atoms.button>
</div>