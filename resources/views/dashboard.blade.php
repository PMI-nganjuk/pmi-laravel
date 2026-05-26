<x-app-layout>
    <x-layout.shell>
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
                                <img src="{{ asset('images/logo.png') }}" alt="Logo PMI" class="h-5 w-5 mr-2 object-contain" />
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
    </x-layout.shell>
</x-app-layout>
