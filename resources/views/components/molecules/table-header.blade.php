@props([
    'column',
    'label'
])

<th 
    scope="col" 
    class="py-4 px-6 text-xs font-bold text-content-muted uppercase tracking-wider"
    x-bind:aria-sort="sortBy === '{{ $column }}' ? (sortDir === 'asc' ? 'ascending' : 'descending') : 'none'"
>
    <button 
        type="button" 
        @click="sortDir = (sortBy === '{{ $column }}' && sortDir === 'asc') ? 'desc' : 'asc'; sortBy = '{{ $column }}'; fetchData()" 
        class="group inline-flex items-center gap-1.5 hover:text-content-base focus:outline-none focus-visible:ring-2 focus-visible:ring-surface-border rounded"
        aria-label="Urutkan berdasarkan {{ $label }}"
    >
        {{ $label }}
        <span class="text-content-subtle group-hover:text-content-muted">
            <template x-if="sortBy === '{{ $column }}'">
                <span>
                    <!-- Ascending Icon -->
                    <svg x-show="sortDir === 'asc'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                    <!-- Descending Icon -->
                    <svg x-show="sortDir === 'desc'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </span>
            </template>
            <template x-if="sortBy !== '{{ $column }}'">
                <!-- Default/Unsorted Icon -->
                <svg class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
            </template>
        </span>
    </button>
</th>