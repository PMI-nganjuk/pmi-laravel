@props([
    'as' => 'input',
    'name' => null,
    'label' => null,
    'required' => false,
    'placeholder' => '',
    'type' => 'text',
    'hint' => null,
    'disabled' => false,
    'isError' => false,
])

@php
    $fieldId = $attributes->get('id') ?? $name;
    $errorName = $name;
    $hasError = $isError || ($errorName && $errors->has($errorName));
    $describedBy = trim(($hint && $fieldId ? $fieldId . '_hint' : '') . ' ' . ($hasError && $fieldId ? $fieldId . '_error' : ''));

    $baseClasses = 'block w-full rounded-xl border bg-surface-base text-sm text-content-base transition duration-200 placeholder:text-content-subtle hover:border-content-subtle focus-visible:border-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-ring disabled:cursor-not-allowed disabled:bg-surface-muted disabled:opacity-60';
    
    $inputClasses = 'px-4 py-2.5 h-11';
    
    $stateClasses = $hasError
        ? 'border-danger text-danger-text focus-visible:border-danger focus-visible:ring-danger/20'
        : 'border-surface-border';
        
    $fieldClasses = $baseClasses . ' ' . $inputClasses . ' ' . $stateClasses;
@endphp

<div class="w-full">
    @if($label && $fieldId)
        <label for="{{ $fieldId }}" class="mb-2 block text-xs font-bold uppercase tracking-normal text-content-base">
            {{ $label }}
            @if($required)
                <span class="text-danger" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($as === 'select')
            <select
                @if($name) name="{{ $name }}" @endif
                @if($fieldId) id="{{ $fieldId }}" @endif
                @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
                @if($hasError) aria-invalid="true" @endif
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes->except('id')->merge(['class' => $fieldClasses . ' appearance-none pr-10']) }}
            >
                {{ $slot }}
            </select>
        @else
            <input
                type="{{ $type }}"
                @if($name) name="{{ $name }}" @endif
                @if($fieldId) id="{{ $fieldId }}" @endif
                placeholder="{{ $placeholder }}"
                @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
                @if($hasError) aria-invalid="true" @endif
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes->except('id')->merge(['class' => $fieldClasses]) }}
            />
        @endif
    </div>

    @if($hint && $fieldId)
        <p id="{{ $fieldId }}_hint" class="mt-1.5 text-xs text-content-muted">{{ $hint }}</p>
    @endif

    @if($errorName)
        @error($errorName)
            <p id="{{ $fieldId }}_error" class="mt-1.5 text-xs font-medium text-danger-text" role="alert">{{ $message }}</p>
        @enderror
    @endif
</div>