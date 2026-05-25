@props([
    'pageTitle' => null,
])

<div {{ $attributes->merge(['class' => 'min-h-screen bg-slate-50 font-sans text-slate-900 flex'])->except('page-title') }}
     @if(!$attributes->has('x-data')) x-data="{ sidebarOpen: false }" @endif>
    
    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
         x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         style="display: none;"></div>

    <!-- Sidebar -->
    <x-layout.sidebar />

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Topbar header -->
        <x-layout.header :page-title="$pageTitle" />

        <!-- Body -->
        {{ $slot }}
    </div>
</div>
