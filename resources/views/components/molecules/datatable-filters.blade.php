@props(['placeholder' => 'Cari data...'])

<div class="p-4 sm:p-6 border-b border-surface-border bg-surface-muted/30">
    <form method="GET" @submit.prevent="fetchData()" class="w-full flex flex-col md:flex-row md:flex-wrap md:items-center gap-4">
        
        <div class="relative w-full md:w-auto md:flex-1 min-w-60.75">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-content-muted z-10">
                <svg x-show="!loading" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <svg x-show="loading" style="display: none;" class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
            
            <x-atoms.input 
                type="text" 
                x-model="filters.search" 
                x-on:input.debounce.400ms="fetchData()"
                class="pl-11 pr-4 w-full"
                placeholder="{{ $placeholder ?? 'Cari data...' }}" 
                aria-label="{{ $placeholder ?? 'Cari data...' }}"
            />
        </div>

        @if(isset($slot) && $slot->isNotEmpty())
            <div class="flex flex-col sm:flex-row items-stretch md:items-center gap-4 w-full md:w-auto min-w-0">
                {{ $slot }}
            </div>
        @endif

        <div class="flex flex-row items-center gap-2.5 w-full md:w-auto justify-stretch sm:justify-end md:ml-auto shrink-0">
            <x-atoms.button type="submit" variant="info" size="md" class="flex-1 sm:flex-none sm:w-auto">
                Cari
            </x-atoms.button>
            <x-atoms.button type="button" 
                    variant="outline" 
                    size="md"
                    x-show="hasActiveFilters" 
                    x-on:click="resetFilters()"
                    style="display: none;"
                    class="flex-1 sm:flex-none sm:w-auto">
                Reset
            </x-atoms.button>
        </div>
    </form>
</div>