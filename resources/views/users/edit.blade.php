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
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Edit Akun Pengguna</h1>
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
            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-3xl mx-auto w-full space-y-6">
                    <!-- Back Link -->
                    <div class="mb-4">
                        <a href="{{ route('users.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-950 transition duration-200 gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Daftar Pengguna
                        </a>
                    </div>

                    <!-- Edit Card Form -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
                        <h2 class="text-lg font-bold text-slate-900 mb-1">Formulir Edit Akun</h2>
                        <p class="text-slate-500 text-xs mb-6">Perbarui informasi akun pengguna dan peran sistemnya.</p>

                        @if ($errors->any())
                            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3.5 rounded-xl" role="alert">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-5">
                            @csrf
                            @method('PUT')

                            <!-- Name field -->
                            <div>
                                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap</label>
                                <input id="name" name="name" type="text" required value="{{ old('name', $user->name) }}"
                                    class="appearance-none block w-full px-4 py-3 bg-white border border-slate-350 hover:border-slate-400 focus:border-red-500 text-slate-900 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/20 transition duration-200"
                                    placeholder="Masukkan nama lengkap">
                            </div>

                            <!-- Email field -->
                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Email</label>
                                <input id="email" name="email" type="email" required value="{{ old('email', $user->email) }}"
                                    class="appearance-none block w-full px-4 py-3 bg-white border border-slate-350 hover:border-slate-400 focus:border-red-500 text-slate-900 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/20 transition duration-200"
                                    placeholder="nama@pmi-nganjuk.or.id">
                            </div>

                            <!-- Role field selection -->
                            <div>
                                <label for="role" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Peran Sistem (Role)</label>
                                <div class="relative">
                                    <select id="role" name="role" required
                                        class="appearance-none block w-full px-4 py-3 bg-white border border-slate-350 hover:border-slate-400 focus:border-red-500 text-slate-900 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/20 transition duration-200">
                                        @foreach ($roles as $r)
                                            <option value="{{ $r->value }}" {{ old('role', $user->role->value) === $r->value ? 'selected' : '' }}>
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
                            </div>

                            <!-- Password field -->
                            <div>
                                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Sandi Baru (Kosongkan jika tidak ingin mengubah)</label>
                                <input id="password" name="password" type="password"
                                    class="appearance-none block w-full px-4 py-3 bg-white border border-slate-350 hover:border-slate-400 focus:border-red-500 text-slate-900 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/20 transition duration-200"
                                    placeholder="Minimal 8 karakter, kosongkan jika tidak diubah">
                            </div>

                            <!-- Action buttons -->
                            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 mt-6">
                                <a href="{{ route('users.index') }}"
                                   class="py-2.5 px-4 bg-white hover:bg-slate-50 border border-slate-200 text-sm font-semibold rounded-xl text-slate-700 transition duration-200">
                                    Batal
                                </a>
                                <button type="submit"
                                        class="py-2.5 px-6 bg-red-600 hover:bg-red-700 text-sm font-semibold rounded-xl text-white transition duration-200 shadow-sm">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
