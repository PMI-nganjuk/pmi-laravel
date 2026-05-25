<div class="flex items-center gap-3 mb-4">
    <div class="h-10 w-10 rounded-full bg-surface-hover flex items-center justify-center border border-surface-border text-content-base font-bold shadow-inner" aria-hidden="true">
        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
    </div>
    <div class="overflow-hidden">
        <h4 class="text-sm font-bold text-content-base truncate" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</h4>
        <span class="text-xs text-content-muted overflow-hidden truncate block" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</span>
    </div>
</div>