<x-app-layout>
    <div class="min-h-screen bg-slate-50 font-sans text-slate-900 flex" x-data="{ sidebarOpen: false }">
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
        <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 flex flex-col justify-between transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               x-cloak>
            <div>
                <!-- Brand logo -->
                <div class="h-16 flex items-center px-6 border-b border-slate-200 gap-3">
                    <div class="p-1.5 bg-red-50 border border-red-200 rounded-lg">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <span class="font-bold text-lg text-slate-900 tracking-wider">PMI Nganjuk</span>
                </div>

                <!-- Navigation menu -->
                <nav class="p-4 space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-slate-650 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition duration-200">
                        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        Ringkasan
                    </a>

                    @if(Auth::user()->hasRole(\App\Enums\RoleEnum::ADMIN))
                        <a href="#" class="flex items-center px-4 py-3 text-slate-650 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition duration-200">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            Konfigurasi Sistem
                        </a>
                        <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 bg-red-50 text-red-600 border-l-4 border-red-500 font-semibold rounded-r-lg transition duration-200">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Manajemen Akun
                        </a>
                    @endif

                    @if(Auth::user()->hasAnyRole([\App\Enums\RoleEnum::ADMIN, \App\Enums\RoleEnum::FINANCIAL_MANAGER, \App\Enums\RoleEnum::FINANCE_STAFF]))
                        <a href="#" class="flex items-center px-4 py-3 text-slate-650 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition duration-200">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Jurnal Keuangan
                        </a>
                        <a href="#" class="flex items-center px-4 py-3 text-slate-650 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition duration-200">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Laporan Finansial
                        </a>
                    @endif

                    <a href="#" class="flex items-center px-4 py-3 text-slate-650 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition duration-200">
                        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profil Saya
                    </a>
                </nav>
            </div>

            <!-- Profile bottom sidebar section -->
            <div class="p-4 border-t border-slate-200">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center border border-slate-200 text-slate-800 font-bold shadow-inner">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <h4 class="text-sm font-bold text-slate-900 truncate">{{ Auth::user()->name }}</h4>
                        <span class="text-xs text-slate-500 overflow-hidden truncate block">{{ Auth::user()->email }}</span>
                    </div>
                </div>
                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center py-2.5 px-4 bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 rounded-xl text-sm font-semibold text-slate-700 hover:text-red-600 transition duration-200">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar header -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6">
                <div class="flex items-center gap-4">
                    <!-- Mobile sidebar toggle -->
                    <button @click="sidebarOpen = true" class="lg:hidden p-1 rounded-lg hover:bg-slate-100 text-slate-650 hover:text-slate-900">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Akun</h1>
                </div>

                <!-- Right profile badge -->
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold px-3 py-1.5 rounded-full border
                        @if(Auth::user()->role === \App\Enums\RoleEnum::ADMIN)
                            bg-red-50 text-red-700 border-red-200
                        @elseif(Auth::user()->role === \App\Enums\RoleEnum::FINANCIAL_MANAGER)
                            bg-purple-50 text-purple-700 border-purple-200
                        @elseif(Auth::user()->role === \App\Enums\RoleEnum::FINANCE_STAFF)
                            bg-blue-50 text-blue-700 border-blue-200
                        @elseif(Auth::user()->role === \App\Enums\RoleEnum::STAFF)
                            bg-amber-50 text-amber-800 border-amber-200
                        @else
                            bg-slate-100 text-slate-700 border-slate-200
                        @endif">
                        {{ Auth::user()->role->getLabel() }}
                    </span>
                </div>
            </header>

            <!-- Dashboard Body -->
            <main class="flex-1 overflow-y-auto p-6 space-y-6">
                <!-- Session Alert -->
                @if (session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl flex items-center gap-3 shadow-sm" role="alert">
                        <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3.5 rounded-2xl flex items-center gap-3 shadow-sm" role="alert">
                        <svg class="h-5 w-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                <!-- User management header card -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 mb-1">Daftar Pengguna Terdaftar</h2>
                        <p class="text-slate-500 text-sm">
                            Kelola akun pengguna, hak akses peran, dan registrasi anggota baru di dalam sistem.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('users.create') }}"
                           class="inline-flex items-center justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Registrasi Pengguna Baru
                        </a>
                    </div>
                </div>

                <!-- Users table card -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col"
                     x-data="{
                        search: '{{ request('search', '') }}',
                        role: '{{ request('role', '') }}',
                        sortBy: '{{ $sortBy ?? 'name' }}',
                        sortDir: '{{ $sortDir ?? 'asc' }}',
                        loading: false,

                        async fetchData(url = null) {
                            this.loading = true;

                            if (!url) {
                                const params = new URLSearchParams({
                                    search: this.search,
                                    role: this.role,
                                    sort_by: this.sortBy,
                                    sort_dir: this.sortDir
                                });
                                url = `{{ route('users.index') }}?${params.toString()}`;
                            } else {
                                const parsedUrl = new URL(url);
                                parsedUrl.searchParams.set('search', this.search);
                                parsedUrl.searchParams.set('role', this.role);
                                parsedUrl.searchParams.set('sort_by', this.sortBy);
                                parsedUrl.searchParams.set('sort_dir', this.sortDir);
                                url = parsedUrl.toString();
                            }

                            window.history.pushState({}, '', url);

                            try {
                                const response = await fetch(url, {
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                });
                                if (response.ok) {
                                    const html = await response.text();
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(html, 'text/html');

                                    const newTableBody = doc.querySelector('#users-table-body');
                                    const currentTableBody = document.querySelector('#users-table-body');
                                    if (newTableBody && currentTableBody) {
                                        currentTableBody.innerHTML = newTableBody.innerHTML;
                                    }

                                    const newPagination = doc.querySelector('#pagination-footer');
                                    const currentPagination = document.querySelector('#pagination-footer');
                                    if (currentPagination && newPagination) {
                                        currentPagination.innerHTML = newPagination.innerHTML;
                                    }
                                }
                            } catch (error) {
                                console.error('Error fetching users:', error);
                            } finally {
                                this.loading = false;
                            }
                        },

                        resetFilters() {
                            this.search = '';
                            this.role = '';
                            this.fetchData();
                        }
                     }"
                     x-on:click="if($event.target.closest('#pagination-footer a')) { $event.preventDefault(); fetchData($event.target.closest('#pagination-footer a').href) }">
                    <!-- Search & Filters -->
                    <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                        <form action="{{ route('users.index') }}" method="GET" @submit.prevent="fetchData()" class="w-full flex flex-col md:flex-row md:items-center gap-4">
                            <!-- Preserving sorting parameters in form inputs -->
                            <input type="hidden" name="sort_by" x-model="sortBy">
                            <input type="hidden" name="sort_dir" x-model="sortDir">

                            <!-- Search Input -->
                            <div class="relative flex-1">
                                <label for="search-input" class="sr-only">Cari Pengguna</label>
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <!-- Search icon (when not loading) -->
                                    <svg x-show="!loading" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <!-- Spinner icon (when loading) -->
                                    <svg x-show="loading" class="animate-spin h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                                <input id="search-input" type="text" x-model="search" x-on:input.debounce.400ms="fetchData()"
                                       class="block w-full pl-11 pr-4 py-2.5 bg-white border border-slate-350 hover:border-slate-400 focus:border-red-500 text-slate-900 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/20 transition duration-200"
                                       placeholder="Cari nama atau email...">
                            </div>

                            <!-- Role Filter Select -->
                            <div class="w-full md:w-64 relative">
                                <label for="role-filter" class="sr-only">Filter Peran</label>
                                <select id="role-filter" x-model="role" x-on:change="fetchData()"
                                        class="appearance-none block w-full pl-4 pr-10 py-2.5 bg-white border border-slate-355 hover:border-slate-400 focus:border-red-500 text-slate-900 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/20 transition duration-200">
                                    <option value="">Semua Peran</option>
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->value }}">
                                            {{ $r->getLabel() }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex items-center gap-2.5 shrink-0">
                                <button type="submit"
                                        class="w-full md:w-auto inline-flex items-center justify-center py-2.5 px-5 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 focus:outline-none transition duration-200">
                                    Cari
                                </button>
                                <button type="button" x-show="search.length > 0 || role !== ''" x-on:click="resetFilters()"
                                       class="w-full md:w-auto inline-flex items-center justify-center py-2.5 px-4 border border-slate-200 rounded-xl bg-white hover:bg-slate-50 text-sm font-semibold text-slate-700 hover:text-slate-900 transition duration-200"
                                       style="display: none;">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Table container -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50">
                                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        <button type="button" @click="sortDir = (sortBy === 'name' && sortDir === 'asc') ? 'desc' : 'asc'; sortBy = 'name'; fetchData()" class="group inline-flex items-center gap-1 hover:text-slate-900 focus:outline-none">
                                            Nama Pengguna
                                            <span class="text-slate-400 group-hover:text-slate-650">
                                                <template x-if="sortBy === 'name'">
                                                    <span>
                                                        <svg x-show="sortDir === 'asc'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7" /></svg>
                                                        <svg x-show="sortDir === 'desc'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" /></svg>
                                                    </span>
                                                </template>
                                                <template x-if="sortBy !== 'name'">
                                                    <svg class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                                                </template>
                                            </span>
                                        </button>
                                    </th>
                                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        <button type="button" @click="sortDir = (sortBy === 'email' && sortDir === 'asc') ? 'desc' : 'asc'; sortBy = 'email'; fetchData()" class="group inline-flex items-center gap-1 hover:text-slate-900 focus:outline-none">
                                            Alamat Email
                                            <span class="text-slate-400 group-hover:text-slate-650">
                                                <template x-if="sortBy === 'email'">
                                                    <span>
                                                        <svg x-show="sortDir === 'asc'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7" /></svg>
                                                        <svg x-show="sortDir === 'desc'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" /></svg>
                                                    </span>
                                                </template>
                                                <template x-if="sortBy !== 'email'">
                                                    <svg class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                                                </template>
                                            </span>
                                        </button>
                                    </th>
                                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Peran (Role)</th>
                                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        <button type="button" @click="sortDir = (sortBy === 'created_at' && sortDir === 'asc') ? 'desc' : 'asc'; sortBy = 'created_at'; fetchData()" class="group inline-flex items-center gap-1 hover:text-slate-900 focus:outline-none">
                                            Tanggal Bergabung
                                            <span class="text-slate-400 group-hover:text-slate-650">
                                                <template x-if="sortBy === 'created_at'">
                                                    <span>
                                                        <svg x-show="sortDir === 'asc'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7" /></svg>
                                                        <svg x-show="sortDir === 'desc'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" /></svg>
                                                    </span>
                                                </template>
                                                <template x-if="sortBy !== 'created_at'">
                                                    <svg class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                                                </template>
                                            </span>
                                        </button>
                                    </th>
                                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="users-table-body" class="divide-y divide-slate-100">
                                @if($users->isEmpty())
                                    <tr>
                                        <td colspan="6" class="py-12 text-center">
                                            <div class="flex flex-col items-center justify-center gap-3">
                                                <div class="p-3 bg-slate-100 rounded-full border border-slate-200 text-slate-400">
                                                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </div>
                                                <p class="text-slate-900 font-bold text-sm">Tidak Ada Pengguna Ditemukan</p>
                                                <p class="text-slate-500 text-xs max-w-xs">Coba sesuaikan kata kunci pencarian atau filter peran Anda untuk menemukan hasil yang dicari.</p>
                                                @if (request()->filled('search') || request()->filled('role'))
                                                    <button type="button" x-on:click="resetFilters()" class="mt-2 inline-flex items-center justify-center py-2 px-4 border border-slate-200 rounded-xl bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition duration-200">
                                                        Reset Pencarian
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($users as $u)
                                        <tr class="hover:bg-slate-50/50 transition duration-150">
                                            <td class="py-4 px-6 flex items-center gap-3">
                                                <div class="h-9 w-9 rounded-full bg-slate-100 flex items-center justify-center border border-slate-200 text-slate-700 text-xs font-bold shadow-inner">
                                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                                </div>
                                                <span class="text-sm font-bold text-slate-900">{{ $u->name }}</span>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-slate-650 font-medium">{{ $u->email }}</td>
                                            <td class="py-4 px-6">
                                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full border
                                                    @if($u->role === \App\Enums\RoleEnum::ADMIN)
                                                        bg-red-50 text-red-700 border-red-200
                                                    @elseif($u->role === \App\Enums\RoleEnum::FINANCIAL_MANAGER)
                                                        bg-purple-50 text-purple-700 border-purple-200
                                                    @elseif($u->role === \App\Enums\RoleEnum::FINANCE_STAFF)
                                                        bg-blue-50 text-blue-700 border-blue-200
                                                    @elseif($u->role === \App\Enums\RoleEnum::STAFF)
                                                        bg-amber-50 text-amber-800 border-amber-200
                                                    @else
                                                        bg-slate-100 text-slate-700 border-slate-200
                                                    @endif">
                                                    {{ $u->role->getLabel() }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-slate-500 font-medium">
                                                {{ $u->created_at ? $u->created_at->format('d M Y, H:i') : '-' }}
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center gap-1.5 text-xs text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 border border-emerald-200 rounded-full">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    Aktif
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-slate-500 font-medium">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('users.edit', $u) }}" class="inline-flex items-center gap-1 py-1.5 px-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg text-slate-700 text-xs font-semibold transition duration-150">
                                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                        Edit
                                                    </a>
                                                    @if (Auth::id() !== $u->id)
                                                        <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->name }}? Tindakan ini tidak dapat dibatalkan.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="inline-flex items-center gap-1 py-1.5 px-2.5 bg-red-50 hover:bg-red-100 border border-red-100 rounded-lg text-red-700 hover:text-red-800 text-xs font-semibold transition duration-150">
                                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                                Hapus
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-xs text-slate-400 italic px-2">Akun Anda</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer wrapper -->
                    <div id="pagination-footer">
                        @if ($users->hasPages())
                            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                                {{ $users->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
