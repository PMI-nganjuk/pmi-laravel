@props([
    'title' => 'Tidak Ada Data',
    'message' => 'Coba sesuaikan kata kunci pencarian atau filter Anda.'
])

<div class="flex flex-col items-center justify-center gap-3 py-12 text-center">
    <div class="p-4 bg-surface-muted rounded-full border border-surface-border text-content-subtle" aria-hidden="true">
        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
    </div>
    <p class="text-content-base font-bold text-sm">{{ $title }}</p>
    <p class="text-content-muted text-xs max-w-xs">{{ $message }}</p>
    
    {{ $slot }}
</div>