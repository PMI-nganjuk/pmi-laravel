<aside class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 flex flex-col justify-between transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen print:hidden"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       x-cloak>
       
    <div class="flex-1 flex flex-col min-h-0 overflow-y-auto">
        <!-- Brand / Logo -->
        <div class="h-16 flex items-center px-6 border-b border-slate-200 gap-3">
            <div class="p-1.5 bg-red-50 border border-red-200 rounded-lg">
                <x-atoms.icon name="logo" class="h-6 w-6 text-red-600" />
            </div>
            <span class="font-bold text-lg text-slate-900 tracking-wider">PMI Nganjuk</span>
        </div>

        <!-- Navigation Menu -->
        <nav class="p-4 space-y-6" x-data="{
            openSections: {
                @foreach($menuItems as $section)
                    @if($section['collapsible'])
                        '{{ $section['key'] }}': {{ $section['default_open'] ? 'true' : 'false' }},
                    @endif
                @endforeach
            }
        }">
            @foreach($menuItems as $section)
                @if(empty($section['items']))
                    @continue
                @endif

                <div class="space-y-1.5">
                    @if($section['collapsible'])
                        {{-- Collapsible Section Header --}}
                        <button type="button" 
                                @click="openSections['{{ $section['key'] }}'] = !openSections['{{ $section['key'] }}']"
                                class="w-full flex items-center justify-between px-4 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 hover:text-slate-600 transition-colors duration-200 focus:outline-none">
                            <span>{{ $section['label'] }}</span>
                            <x-atoms.icon name="chevron-down" 
                                          class="h-3.5 w-3.5 text-slate-400 transform transition-transform duration-200" 
                                          x-bind:class="openSections['{{ $section['key'] }}'] ? 'rotate-180' : ''" />
                        </button>

                        {{-- Section Content --}}
                        <div x-show="openSections['{{ $section['key'] }}']"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-1 pl-1">
                            @foreach($section['items'] as $item)
                                <x-molecules.nav-link 
                                    :route="$item['route']" 
                                    :icon="$item['icon']" 
                                    :label="$item['label']" 
                                    :active="$item['active']" 
                                />
                            @endforeach
                        </div>
                    @else
                        {{-- Non-collapsible Section Header --}}
                        @if($section['label'])
                            <div class="px-4 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 select-none">
                                {{ $section['label'] }}
                            </div>
                        @endif
                        
                        <div class="space-y-1">
                            @foreach($section['items'] as $item)
                                <x-molecules.nav-link 
                                    :route="$item['route']" 
                                    :icon="$item['icon']" 
                                    :label="$item['label']" 
                                    :active="$item['active']" 
                                />
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
            
            <!-- Static link: Profile -->
            <div class="pt-4 border-t border-slate-100">
                <x-molecules.nav-link route="profile.show" icon="user" label="Profil Saya" />
            </div>
        </nav>
    </div>

    <!-- User Section -->
    <div class="p-4 border-t border-slate-200">
        <x-molecules.user-profile />
        
        <!-- Logout Button -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center py-2.5 px-4 bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 rounded-xl text-sm font-semibold text-slate-700 hover:text-red-600 transition duration-200"
                aria-label="Keluar dari aplikasi">
                <x-atoms.icon name="logout" class="h-4 w-4 mr-2" />
                Keluar
            </button>
        </form>
    </div>
</aside>