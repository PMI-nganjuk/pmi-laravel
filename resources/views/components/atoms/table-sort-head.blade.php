@props(['column', 'label' => null])

<x-atoms.table-head {{ $attributes }}>
    <button type="button" 
            @click="toggleSort('{{ $column }}')" 
            class="group inline-flex items-center gap-1 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-ring hover:text-content-base transition-colors">
        {{ $label ?? $slot }}
        
        <span class="text-content-subtle group-hover:text-content-muted">
            <!-- Icon ASC -->
            <svg x-show="sortBy === '{{ $column }}' && sortDir === 'asc'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7" />
            </svg>
            <!-- Icon DESC -->
            <svg x-show="sortBy === '{{ $column }}' && sortDir === 'desc'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
            </svg>
            <!-- Icon Inactive -->
            <svg x-show="sortBy !== '{{ $column }}'" class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
            </svg>
        </span>
    </button>
</x-atoms.table-head>
