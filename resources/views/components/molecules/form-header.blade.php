@props([
    'badge' => null,
    'title' => null,
    'subtitle' => null,
    
    'badgeXText' => null,
    'titleXText' => null,
    'subtitleXText' => null,
])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col justify-between gap-4 border-b border-surface-border pb-5 sm:flex-row sm:items-start']) }}>
    <div>
        @if($badge || $badgeXText)
            <p class="text-xs font-bold uppercase tracking-normal text-primary" 
               @if($badgeXText) x-text="{{ $badgeXText }}" @endif>
                {{ $badge }}
            </p>
        @endif

        <h2 class="mt-1 text-lg font-bold text-content-base" 
            @if($titleXText) x-text="{{ $titleXText }}" @endif>
            {{ $title }}
        </h2>

        @if($subtitle || $subtitleXText)
            <p class="mt-1 text-sm text-content-muted" 
               @if($subtitleXText) x-text="{{ $subtitleXText }}" @endif>
                {{ $subtitle }}
            </p>
        @endif
    </div>
</div>