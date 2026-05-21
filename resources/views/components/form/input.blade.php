@props(['name', 'label', 'required' => false, 'placeholder' => '', 'type' => 'text'])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}"
        name="{{ $name }}" 
        id="{{ $name }}"
        placeholder="{{ $placeholder }}"
        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        {{ $required ? 'required' : '' }}
        {{ $attributes->except('class') }}
    />
</div>
