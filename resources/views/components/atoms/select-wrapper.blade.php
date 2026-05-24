@props([
    'name' => null,
    'label' => null,
    'required' => false,
    'disabled' => false,
    'hint' => null,
    'hasError' => false,
])

@php
    $fieldId = $attributes->get('id') ?? $name;
    $describedBy = trim(($hint && $fieldId ? $fieldId . '_hint' : '') . ' ' . ($hasError && $fieldId ? $fieldId . '_error' : ''));

    $baseClasses = 'block w-full appearance-none rounded-xl border bg-surface-base text-sm text-content-base shadow-sm transition duration-200 placeholder:text-content-subtle px-4 py-3 pr-10 hover:border-content-subtle focus-visible:border-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-ring disabled:cursor-not-allowed disabled:bg-surface-muted disabled:opacity-60';
    $stateClasses = $hasError
        ? 'border-danger text-danger-text focus-visible:border-danger focus-visible:ring-danger/20'
        : 'border-surface-border';
    $fieldClasses = $baseClasses . ' ' . $stateClasses;
@endphp

<div>
    @if($label && $fieldId)
        <label for="{{ $fieldId }}" class="mb-2 block text-xs font-bold uppercase tracking-normal text-content-base">
            {{ $label }}
            @if($required)
                <span class="text-danger" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <select
            @if($name) name="{{ $name }}" @endif
            @if($fieldId) id="{{ $fieldId }}" @endif
            @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if($hasError) aria-invalid="true" @endif
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except('id')->merge(['class' => $fieldClasses]) }}
        >
            {{ $slot }}
        </select>

        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-content-muted" aria-hidden="true">
            <x-atoms.icon name="chevron-down" class="h-4 w-4" />
        </div>
    </div>

    @if($hint && $fieldId)
        <p id="{{ $fieldId }}_hint" class="mt-1.5 text-xs text-content-muted">{{ $hint }}</p>
    @endif

    @if($hasError && $fieldId && $errors->has($name))
        @error($name)
            <p id="{{ $fieldId }}_error" class="mt-1.5 text-xs font-medium text-danger-text" role="alert">{{ $message }}</p>
        @enderror
    @endif
</div>
