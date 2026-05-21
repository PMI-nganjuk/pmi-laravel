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
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 bg-red-50 text-red-600 border-l-4 border-red-500 font-semibold rounded-r-lg transition duration-200">
                        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        Ringkasan
                    </a>
                    
                    @if($user->hasRole(\App\Enums\RoleEnum::ADMIN))
                        <a href="#" class="flex items-center px-4 py-3 text-slate-650 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition duration-200">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            Konfigurasi Sistem
                        </a>
                        <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 text-slate-650 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition duration-200">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Manajemen Akun
                        </a>
                    @endif

                    @if($user->hasAnyRole([\App\Enums\RoleEnum::ADMIN, \App\Enums\RoleEnum::FINANCIAL_MANAGER, \App\Enums\RoleEnum::FINANCE_STAFF]))
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
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <h4 class="text-sm font-bold text-slate-900 truncate">{{ $user->name }}</h4>
                        <span class="text-xs text-slate-500 overflow-hidden truncate block">{{ $user->email }}</span>
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
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard Ringkasan</h1>
                </div>

                <!-- Right profile badge -->
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold px-3 py-1.5 rounded-full border
                        @if($user->role === \App\Enums\RoleEnum::ADMIN)
                            bg-red-50 text-red-700 border-red-200
                        @elseif($user->role === \App\Enums\RoleEnum::FINANCIAL_MANAGER)
                            bg-purple-50 text-purple-700 border-purple-200
                        @elseif($user->role === \App\Enums\RoleEnum::FINANCE_STAFF)
                            bg-blue-50 text-blue-700 border-blue-200
                        @elseif($user->role === \App\Enums\RoleEnum::STAFF)
                            bg-amber-50 text-amber-800 border-amber-200
                        @else
                            bg-slate-100 text-slate-700 border-slate-200
                        @endif">
                        {{ $user->role->getLabel() }}
                    </span>
                </div>
            </header>

            <!-- Dashboard Body -->
            <main class="flex-1 overflow-y-auto p-6 space-y-6">
                <!-- Welcome card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm relative overflow-hidden group">
                    <div class="relative z-10 max-w-lg">
                        <h2 class="text-2xl font-bold text-slate-900 mb-2">Selamat Datang Kembali, {{ $user->name }}!</h2>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            Anda masuk sebagai <strong class="text-red-650 font-semibold">{{ $user->role->getLabel() }}</strong>. Anda dapat melihat ringkasan, riwayat, dan laporan manajemen keuangan sesuai dengan wewenang Anda.
                        </p>
                    </div>
                </div>

                <!-- Stats widgets (Bento Grid) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @if($user->hasRole(\App\Enums\RoleEnum::ADMIN))
                        <!-- Admin Specific Stats -->
                        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex flex-col justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pengguna Terdaftar</span>
                            <div class="flex items-baseline justify-between mt-4">
                                <span class="text-3xl font-extrabold text-slate-900">{{ $stats['total_users'] }}</span>
                                <span class="text-xs text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 border border-emerald-100 rounded-full">Aktif</span>
                            </div>
                        </div>
                        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex flex-col justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sesi Pengguna Aktif</span>
                            <div class="flex items-baseline justify-between mt-4">
                                <span class="text-3xl font-extrabold text-slate-900">{{ $stats['active_sessions'] }}</span>
                                <span class="text-xs text-blue-600 font-semibold bg-blue-50 px-2 py-0.5 border border-blue-100 rounded-full">Real-time</span>
                            </div>
                        </div>
                        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex flex-col justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Beban CPU Server</span>
                            <div class="flex items-baseline justify-between mt-4">
                                <span class="text-3xl font-extrabold text-slate-900">{{ $stats['system_load'] }}</span>
                                <span class="text-xs text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 border border-emerald-100 rounded-full">Optimal</span>
                            </div>
                        </div>
                    @endif

                    @if($user->hasAnyRole([\App\Enums\RoleEnum::FINANCIAL_MANAGER, \App\Enums\RoleEnum::FINANCE_STAFF]))
                        <!-- Financial Stats -->
                        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex flex-col justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Saldo Kas</span>
                            <div class="flex items-baseline justify-between mt-4">
                                <span class="text-2xl font-extrabold text-slate-900">{{ $stats['cash_balance'] }}</span>
                                <span class="text-xs text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 border border-emerald-100 rounded-full">Lancar</span>
                            </div>
                        </div>
                        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex flex-col justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Penerimaan Hibah/Donasi</span>
                            <div class="flex items-baseline justify-between mt-4">
                                <span class="text-2xl font-extrabold text-slate-900">{{ $stats['monthly_donations'] }}</span>
                                <span class="text-xs text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 border border-emerald-100 rounded-full">+12.4%</span>
                            </div>
                        </div>
                        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex flex-col justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pengeluaran Bulan Ini</span>
                            <div class="flex items-baseline justify-between mt-4">
                                <span class="text-2xl font-extrabold text-slate-900">{{ $stats['expenses_this_month'] }}</span>
                                <span class="text-xs text-red-600 font-semibold bg-red-50 px-2 py-0.5 border border-red-100 rounded-full">Anggaran</span>
                            </div>
                        </div>
                    @endif

                    @if($user->hasRole(\App\Enums\RoleEnum::STAFF) || $user->hasRole(\App\Enums\RoleEnum::USER))
                        <!-- Staff and General User Stats -->
                        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex flex-col justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tugas Dikirim</span>
                            <div class="flex items-baseline justify-between mt-4">
                                <span class="text-3xl font-extrabold text-slate-900">{{ $stats['submitted_tasks'] }}</span>
                                <span class="text-xs text-amber-605 font-semibold bg-amber-50 px-2 py-0.5 border border-amber-100 rounded-full">Perlu Cek</span>
                            </div>
                        </div>
                        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex flex-col justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Laporan Disetujui</span>
                            <div class="flex items-baseline justify-between mt-4">
                                <span class="text-3xl font-extrabold text-slate-900">{{ $stats['completed_reports'] }}</span>
                                <span class="text-xs text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 border border-emerald-100 rounded-full">Selesai</span>
                            </div>
                        </div>
                        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex flex-col justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Aktivitas Sistem</span>
                            <div class="flex items-baseline justify-between mt-4">
                                <span class="text-base font-bold text-slate-800">Tidak Ada Kendala</span>
                                <span class="text-xs text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 border border-emerald-100 rounded-full">Normal</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Role specific actions & details (Grid Layout) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Action List -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-red-650" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tindakan Cepat & Alur Kerja
                        </h3>
                        
                        <div class="space-y-3">
                            @if($user->hasRole(\App\Enums\RoleEnum::ADMIN))
                                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-bold text-slate-900 block">Audit Akses RBAC</span>
                                        <span class="text-xs text-slate-500">Verifikasi lisensi, akses log, dan otorisasi peran</span>
                                    </div>
                                    <button class="px-3.5 py-2 bg-red-600 hover:bg-red-700 text-xs font-bold rounded-lg text-white transition">Mulai</button>
                                </div>
                                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-bold text-slate-900 block">Cadangkan Database</span>
                                        <span class="text-xs text-slate-500">Buat cadangan data instan dan unduh salinan SQLite</span>
                                    </div>
                                    <button class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-xs font-bold rounded-lg text-slate-700 transition">Jalankan</button>
                                </div>
                            @endif

                            @if($user->role === \App\Enums\RoleEnum::FINANCIAL_MANAGER)
                                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-bold text-slate-900 block">Persetujuan Pencairan Kas</span>
                                        <span class="text-xs text-slate-500">Ada {{ $stats['pending_approvals'] }} pengajuan pengeluaran kas yang butuh validasi</span>
                                    </div>
                                    <button class="px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-xs font-bold rounded-lg text-white transition">Periksa</button>
                                </div>
                            @endif

                            @if($user->hasAnyRole([\App\Enums\RoleEnum::FINANCIAL_MANAGER, \App\Enums\RoleEnum::FINANCE_STAFF]))
                                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-bold text-slate-900 block">Pencatatan Jurnal Baru</span>
                                        <span class="text-xs text-slate-500">Catat transaksi kas masuk atau kas keluar terkini</span>
                                    </div>
                                    <button class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-xs font-bold rounded-lg text-white transition">Input Jurnal</button>
                                </div>
                            @endif

                            @if($user->hasRole(\App\Enums\RoleEnum::STAFF) || $user->hasRole(\App\Enums\RoleEnum::USER))
                                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-bold text-slate-900 block">Kirim Laporan Harian</span>
                                        <span class="text-xs text-slate-500">Laporkan capaian kegiatan operasional hari ini</span>
                                    </div>
                                    <button class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-xs font-bold rounded-lg text-white transition">Buat Laporan</button>
                                </div>
                                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-bold text-slate-900 block">Lihat Log Aktivitas Pribadi</span>
                                        <span class="text-xs text-slate-500">Pantau riwayat aksi login dan modifikasi data pribadi</span>
                                    </div>
                                    <button class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-xs font-bold rounded-lg text-slate-700 transition">Lihat</button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Role Behavior Telemetry description -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 mb-3 flex items-center">
                                <svg class="h-5 w-5 mr-2 text-red-650" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Hak Akses Otoritas Peran
                            </h3>
                            <p class="text-slate-500 text-sm leading-relaxed mb-4">
                                Berdasarkan logika RBAC terpusat, sistem membagi wewenang peran Anda menjadi skenario berikut:
                            </p>
                            
                            <ul class="space-y-2.5 text-xs">
                                <li class="flex items-start gap-2">
                                    <span class="h-1.5 w-1.5 bg-red-500 rounded-full mt-1.5 shrink-0"></span>
                                    <span class="text-slate-700"><strong>Administrator (Admin):</strong> Memiliki bypass penuh pada seluruh pemeriksaan gerbang otorisasi.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="h-1.5 w-1.5 bg-purple-500 rounded-full mt-1.5 shrink-0"></span>
                                    <span class="text-slate-700"><strong>Manager Keuangan:</strong> Berwenang memvalidasi pengeluaran kas dan memeriksa seluruh laporan keuangan bulanan.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="h-1.5 w-1.5 bg-blue-500 rounded-full mt-1.5 shrink-0"></span>
                                    <span class="text-slate-700"><strong>Staf Keuangan:</strong> Dapat memasukkan catatan transaksi kas baru, namun tidak memiliki wewenang persetujuan pencairan.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="h-1.5 w-1.5 bg-amber-500 rounded-full mt-1.5 shrink-0"></span>
                                    <span class="text-slate-700"><strong>Karyawan & Pengguna Umum:</strong> Memiliki perilaku setara dengan wewenang terbatas (read-only untuk modul pencatatan finansial).</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-slate-200 text-[11px] text-slate-400 flex items-center justify-between">
                            <span>Sistem RBAC Terintegrasi</span>
                            <span>Ver. 1.0.0</span>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
