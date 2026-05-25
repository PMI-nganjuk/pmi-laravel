@props(['route' => null, 'icon', 'label', 'active' => false, 'disabled' => false])

@php
    $isRouteValid = $route && Route::has($route);
    $href = $isRouteValid ? route($route) : '#';
    $isDisabled = $disabled || !$isRouteValid;
@endphp

<a href="{{ $href }}" 
    @if($isDisabled)
       aria-disabled="true"
       tabindex="-1"
       onclick="event.preventDefault();"
   @else
       aria-current="{{ $active ? 'page' : 'false' }}"
   @endif

   @class([
       'flex items-center px-4 py-3 rounded-lg transition duration-200 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500',
       'bg-primary-50 text-primary-600 border-l-4 border-primary-500 font-semibold rounded-l-none' => $active && !$isDisabled, 
       'text-content-muted hover:bg-surface-hover hover:text-content-base' => !$active && !$isDisabled,
       'opacity-50 cursor-not-allowed bg-transparent text-content-subtle' => $isDisabled,
   ])
   aria-current="{{ $active ? 'page' : 'false' }}">
   
    <x-atoms.icon :name="$icon" 
        @class([
            'mr-3 transition-colors duration-200',
            'text-primary-600' => $active,
            'text-content-subtle group-hover:text-content-muted' => !$active
        ]) 
    />
    
    <span>{{ $label }}</span>

    @if($isDisabled)
        <span class="ml-auto text-[10px] font-bold uppercase tracking-wider text-content-subtle bg-surface-border px-2 py-0.5 rounded">
            WIP
        </span>
    @endif
</a>