<aside class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 flex flex-col justify-between transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
       
    <div>
        <!-- Brand / Logo -->
        <div class="h-16 flex items-center px-6 border-b border-slate-200 gap-3">
            <div class="p-1.5 bg-red-50 border border-red-200 rounded-lg">
                <x-atoms.icon name="logo" class="h-6 w-6 text-red-600" />
            </div>
            <span class="font-bold text-lg text-slate-900 tracking-wider">PMI Nganjuk</span>
        </div>

        <!-- Navigation Menu -->
        <nav class="p-4 space-y-1">
            @foreach($menuItems as $item)
                <x-molecules.nav-link 
                    :route="$item['route']" 
                    :icon="$item['icon']" 
                    :label="$item['label']" 
                    :active="$item['active']" 
                />
            @endforeach
            
            <!-- Static links like profile can stay here or be moved to the array -->
            <div class="pt-4 mt-4 border-t border-slate-200">
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