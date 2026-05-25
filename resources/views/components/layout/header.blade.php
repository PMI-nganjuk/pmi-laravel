<header class="h-16 bg-surface-base border-b border-surface-border flex items-center justify-between px-6 transition-colors duration-200" 
        role="banner" 
        aria-label="Site Header">
        
    <div class="flex items-center gap-4">
        <!-- Mobile Sidebar Toggle -->
        <button @click="sidebarOpen = true" 
                class="lg:hidden p-1.5 rounded-lg text-content-subtle hover:bg-surface-hover hover:text-content-base focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-colors duration-200"
                aria-label="Buka navigasi sidebar"
                aria-haspopup="menu"
                :aria-expanded="sidebarOpen.toString()">
            <x-atoms.icon name="menu" class="h-6 w-6" />
        </button>
        
        <!-- Page Title Dynamic -->
        <h1 class="text-xl font-bold text-content-base tracking-tight" id="page-title">
            {{ $pageTitle }}
        </h1>
    </div>

    <!-- Right Profile Information -->
    <div class="flex items-center gap-3">
        @if($user && $user->role)
            <!-- Role Badge -->
            <x-atoms.badge 
                :variant="$user->role->getBadgeVariant()" 
                aria-label="Peran pengguna saat ini: {{ $user->role->getLabel() }}">
                {{ $user->role->getLabel() }}
            </x-atoms.badge>
        @endif
    </div>
</header>