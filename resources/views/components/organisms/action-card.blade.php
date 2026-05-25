@props([
    'title',
    'description',
    'actionLabel',
    'actionClick' => null
])

<x-atoms.surface class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="space-y-1">
        <h2 class="text-xl font-bold tracking-tight text-slate-900">{{ $title }}</h2>
        <p class="text-slate-500 text-sm max-w-2xl">{{ $description }}</p>
    </div>
    <div>
        <x-atoms.button 
            variant="primary" 
            x-on:click="{{ $actionClick }}"
            aria-label="{{ $actionLabel }}"
        >
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            {{ $actionLabel }}
        </x-atoms.button>
    </div>
</x-atoms.surface>