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

                    @if($user->hasRole(\App\Enums\RoleEnum::FINANCE_STAFF))
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

                <!-- Financial Progress Chart & Telemetry (Grid Layout) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Graph Chart Panel (2 cols on large screen) -->
                    <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900">
                                        Grafik Perkembangan Keuangan
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Tren Penerimaan Kas vs Pengeluaran Kas (6 Bulan Terakhir)</p>
                                </div>
                                <div class="flex items-center gap-3 text-xs font-semibold">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Penerimaan
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-700 border border-red-200">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Pengeluaran
                                    </span>
                                </div>
                            </div>

                            <!-- Canvas Chart Container -->
                            <div class="relative w-full h-72">
                                <canvas id="financialTrendChart"></canvas>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span>Data otomatis diperbarui dari entri transaksi kas</span>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('manual-book.download-docx') }}?v={{ time() }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-bold">
                                    Unduh (.docx)
                                </a>
                                <a href="{{ route('manual-book.download') }}?v={{ time() }}" target="_blank" class="inline-flex items-center gap-1 text-red-600 hover:text-red-700 font-bold">
                                    Unduh (.pdf)
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Role Behavior Telemetry description (1 col) -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 mb-3">
                                Hak Akses Otoritas Peran
                            </h3>
                            <p class="text-slate-500 text-sm leading-relaxed mb-4">
                                Berdasarkan logika RBAC terpusat, sistem membagi wewenang peran Anda menjadi skenario berikut:
                            </p>
                            
                            <ul class="space-y-3 text-xs">
                                <li class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="h-2 w-2 bg-red-500 rounded-full mt-1 shrink-0"></span>
                                    <span class="text-slate-700"><strong>Administrator (Admin):</strong> Memiliki bypass penuh pada seluruh pemeriksaan gerbang otorisasi & manajemen sistem.</span>
                                </li>
                                <li class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="h-2 w-2 bg-blue-500 rounded-full mt-1 shrink-0"></span>
                                    <span class="text-slate-700"><strong>Staf Keuangan:</strong> Dapat memasukkan catatan transaksi kas baru & jurnal penyesuaian.</span>
                                </li>
                                <li class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="h-2 w-2 bg-amber-500 rounded-full mt-1 shrink-0"></span>
                                    <span class="text-slate-700"><strong>Karyawan & Pengguna Umum:</strong> Memiliki wewenang terbatas (read-only untuk modul pencatatan finansial & laporan).</span>
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

    <!-- Chart.js Library & Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('financialTrendChart');
            if (!ctx) return;

            const chartData = @js($chartData);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Penerimaan Kas',
                            data: chartData.receipts,
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5, 150, 105, 0.08)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#059669',
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Pengeluaran Kas',
                            data: chartData.disbursements,
                            borderColor: '#DC2626',
                            backgroundColor: 'rgba(220, 38, 38, 0.08)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#DC2626',
                            pointHoverRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    let value = context.raw || 0;
                                    return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Segoe UI', size: 11 }, color: '#64748B' }
                        },
                        y: {
                            grid: { color: '#F1F5F9' },
                            ticks: {
                                font: { family: 'Segoe UI', size: 11 },
                                color: '#64748B',
                                callback: function (value) {
                                    return 'Rp ' + (value / 1000000).toFixed(0) + ' Jt';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>

