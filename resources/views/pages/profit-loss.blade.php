<x-app-layout>
    <x-layout.shell page-title="Laporan Laba Rugi (Aktivitas)">
        <main class="flex-1 overflow-y-auto p-6 space-y-6">
            @if (session('success'))
                <div class="print:hidden">
                    <x-atoms.alert variant="success">
                        {{ session('success') }}
                    </x-atoms.alert>
                </div>
            @endif

            @if (session('error'))
                <div class="print:hidden">
                    <x-atoms.alert variant="danger">
                        {{ session('error') }}
                    </x-atoms.alert>
                </div>
            @endif

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                                Laporan Laba Rugi (Aktivitas)
                            </h1>
                            <p class="text-sm text-slate-500 mt-1">
                                Periode: 
                                <span class="font-semibold text-slate-700">
                                    {{ \Carbon\Carbon::parse($report['period']['start'])->format('d/m/Y') }}
                                </span>
                                s.d.
                                <span class="font-semibold text-slate-700">
                                    {{ \Carbon\Carbon::parse($report['period']['end'])->format('d/m/Y') }}
                                </span>
                            </p>
                        </div>

                        <form method="GET" action="{{ route('profit-loss.index') }}" 
                              class="flex items-end gap-3 flex-wrap print:hidden"
                              id="pl-filter-form">
                            <div class="flex flex-col">
                                <label for="start_date" class="text-xs font-medium text-slate-500 mb-1">Dari</label>
                                <input type="date" name="start_date" id="start_date"
                                       value="{{ old('start_date', request('start_date', $report['period']['start'])) }}"
                                       class="px-3 py-2 text-sm border @error('start_date') border-red-500 @else border-slate-200 @enderror rounded-lg focus:ring-2 focus:ring-red-500/20 focus:border-red-400 transition-colors">
                                @error('start_date')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex flex-col">
                                <label for="end_date" class="text-xs font-medium text-slate-500 mb-1">Sampai</label>
                                <input type="date" name="end_date" id="end_date"
                                       value="{{ old('end_date', request('end_date', $report['period']['end'])) }}"
                                       class="px-3 py-2 text-sm border @error('end_date') border-red-500 @else border-slate-200 @enderror rounded-lg focus:ring-2 focus:ring-red-500/20 focus:border-red-400 transition-colors">
                                @error('end_date')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200 h-[38px] self-end">
                                Terapkan
                            </button>
                            <a href="{{ route('profit-loss.export', request()->query()) }}"
                               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200 flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Ekspor Excel
                            </a>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="pl-report-table">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="text-left px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500 w-[45%]">
                                    Uraian
                                </th>
                                <th class="text-right px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500 w-[18%]">
                                    Pendapatan Tidak Terikat
                                </th>
                                <th class="text-right px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500 w-[18%]">
                                    Pendapatan Terikat
                                </th>
                                <th class="text-right px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500 w-[19%]">
                                    Total Pendapatan
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">

                            <tr class="bg-emerald-50/50">
                                <td colspan="4" class="px-6 py-3 text-sm font-bold text-emerald-800 uppercase tracking-wide">
                                    Pendapatan
                                </td>
                            </tr>

                            @forelse ($report['pendapatan'] as $section)
                                <tr class="bg-emerald-50/30">
                                    <td class="px-6 py-2.5 pl-10 text-sm font-bold text-emerald-700">
                                        {{ $section['name'] }}
                                    </td>
                                    <td class="px-6 py-2.5 text-right font-bold tabular-nums whitespace-nowrap text-emerald-700">
                                        {{ $section['subtotal']['tidak_terikat'] != 0 ? 'Rp ' . number_format($section['subtotal']['tidak_terikat'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-6 py-2.5 text-right font-bold tabular-nums whitespace-nowrap text-emerald-700">
                                        {{ $section['subtotal']['terikat'] != 0 ? 'Rp ' . number_format($section['subtotal']['terikat'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-6 py-2.5 text-right font-bold tabular-nums whitespace-nowrap text-emerald-700">
                                        {{ $section['subtotal']['total'] != 0 ? 'Rp ' . number_format($section['subtotal']['total'], 0, ',', '.') : '—' }}
                                    </td>
                                </tr>

                                @foreach ($section['accounts'] as $account)
                                    @if (count($section['accounts']) === 1 && $account['nama'] === $section['name'])
                                        @continue
                                    @endif
                                    <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                        <td class="px-6 py-2 pl-14 text-slate-700 italic">
                                            {{ $account['nama'] }}
                                        </td>
                                        <td class="px-6 py-2 text-right font-normal italic tabular-nums whitespace-nowrap {{ $account['tidak_terikat'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                            {{ $account['tidak_terikat'] != 0 ? 'Rp ' . number_format($account['tidak_terikat'], 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="px-6 py-2 text-right font-normal italic tabular-nums whitespace-nowrap {{ $account['terikat'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                            {{ $account['terikat'] != 0 ? 'Rp ' . number_format($account['terikat'], 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="px-6 py-2 text-right font-normal italic tabular-nums whitespace-nowrap {{ $account['total'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                            {{ $account['total'] != 0 ? 'Rp ' . number_format($account['total'], 0, ',', '.') : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-3 text-center text-slate-400 italic">
                                        Tidak ada data pendapatan pada periode ini.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="bg-emerald-100/60 border-t-2 border-emerald-300">
                                <td class="px-6 py-3 text-sm font-bold text-emerald-900 uppercase italic">
                                    Total Pendapatan
                                </td>
                                <td class="px-6 py-3 text-right font-bold italic tabular-nums whitespace-nowrap text-emerald-900">
                                    {{ $report['total_pendapatan']['tidak_terikat'] != 0 ? 'Rp ' . number_format($report['total_pendapatan']['tidak_terikat'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-3 text-right font-bold italic tabular-nums whitespace-nowrap text-emerald-900">
                                    {{ $report['total_pendapatan']['terikat'] != 0 ? 'Rp ' . number_format($report['total_pendapatan']['terikat'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-3 text-right font-bold italic tabular-nums whitespace-nowrap text-emerald-900">
                                    {{ $report['total_pendapatan']['total'] != 0 ? 'Rp ' . number_format($report['total_pendapatan']['total'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            <tr><td colspan="4" class="py-2"></td></tr>

                            <tr class="bg-amber-50/50 border-t border-amber-200">
                                <td class="px-6 py-3 text-sm font-bold text-amber-800 uppercase tracking-wide">
                                    Beban Program
                                </td>
                                <td class="px-6 py-3 text-right font-bold text-amber-800 tabular-nums whitespace-nowrap">
                                    {{ $report['total_beban_program']['tidak_terikat'] != 0 ? 'Rp ' . number_format($report['total_beban_program']['tidak_terikat'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-3 text-right font-bold text-amber-800 tabular-nums whitespace-nowrap">
                                    {{ $report['total_beban_program']['terikat'] != 0 ? 'Rp ' . number_format($report['total_beban_program']['terikat'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-3 text-right font-bold text-amber-800 tabular-nums whitespace-nowrap">
                                    {{ $report['total_beban_program']['total'] != 0 ? 'Rp ' . number_format($report['total_beban_program']['total'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            @forelse ($report['beban_program'] as $section)
                                <tr class="bg-amber-50/30">
                                    <td class="px-6 py-2.5 pl-10 text-sm font-bold text-amber-700">
                                        {{ $section['name'] }}
                                    </td>
                                    <td class="px-6 py-2.5 text-right font-bold tabular-nums whitespace-nowrap text-amber-700">
                                        {{ $section['subtotal']['tidak_terikat'] != 0 ? 'Rp ' . number_format($section['subtotal']['tidak_terikat'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-6 py-2.5 text-right font-bold tabular-nums whitespace-nowrap text-amber-700">
                                        {{ $section['subtotal']['terikat'] != 0 ? 'Rp ' . number_format($section['subtotal']['terikat'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-6 py-2.5 text-right font-bold tabular-nums whitespace-nowrap text-amber-700">
                                        {{ $section['subtotal']['total'] != 0 ? 'Rp ' . number_format($section['subtotal']['total'], 0, ',', '.') : '—' }}
                                    </td>
                                </tr>

                                @foreach ($section['accounts'] as $account)
                                    @if (count($section['accounts']) === 1 && $account['nama'] === $section['name'])
                                        @continue
                                    @endif
                                    <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                        <td class="px-6 py-2 pl-14 text-slate-700 italic">
                                            {{ $account['nama'] }}
                                        </td>
                                        <td class="px-6 py-2 text-right font-normal italic tabular-nums whitespace-nowrap {{ $account['tidak_terikat'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                            {{ $account['tidak_terikat'] != 0 ? 'Rp ' . number_format($account['tidak_terikat'], 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="px-6 py-2 text-right font-normal italic tabular-nums whitespace-nowrap {{ $account['terikat'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                            {{ $account['terikat'] != 0 ? 'Rp ' . number_format($account['terikat'], 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="px-6 py-2 text-right font-normal italic tabular-nums whitespace-nowrap {{ $account['total'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                            {{ $account['total'] != 0 ? 'Rp ' . number_format($account['total'], 0, ',', '.') : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-3 text-center text-slate-400 italic">
                                        Tidak ada data beban program pada periode ini.
                                    </td>
                                </tr>
                            @endforelse

                            <tr><td colspan="4" class="py-2"></td></tr>

                            <tr class="bg-rose-50/50 border-t border-rose-200">
                                <td class="px-6 py-3 text-sm font-bold text-rose-800 uppercase tracking-wide">
                                    Beban Manajemen Umum
                                </td>
                                <td class="px-6 py-3 text-right font-bold text-rose-800 tabular-nums whitespace-nowrap">
                                    {{ $report['total_beban_manajemen_umum']['tidak_terikat'] != 0 ? 'Rp ' . number_format($report['total_beban_manajemen_umum']['tidak_terikat'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-3 text-right font-bold text-rose-800 tabular-nums whitespace-nowrap">
                                    {{ $report['total_beban_manajemen_umum']['terikat'] != 0 ? 'Rp ' . number_format($report['total_beban_manajemen_umum']['terikat'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-3 text-right font-bold text-rose-800 tabular-nums whitespace-nowrap">
                                    {{ $report['total_beban_manajemen_umum']['total'] != 0 ? 'Rp ' . number_format($report['total_beban_manajemen_umum']['total'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            @forelse ($report['beban_manajemen_umum'] as $section)
                                <tr class="bg-rose-50/30">
                                    <td class="px-6 py-2.5 pl-10 text-sm font-bold text-rose-700">
                                        {{ $section['name'] }}
                                    </td>
                                    <td class="px-6 py-2.5 text-right font-bold tabular-nums whitespace-nowrap text-rose-700">
                                        {{ $section['subtotal']['tidak_terikat'] != 0 ? 'Rp ' . number_format($section['subtotal']['tidak_terikat'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-6 py-2.5 text-right font-bold tabular-nums whitespace-nowrap text-rose-700">
                                        {{ $section['subtotal']['terikat'] != 0 ? 'Rp ' . number_format($section['subtotal']['terikat'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-6 py-2.5 text-right font-bold tabular-nums whitespace-nowrap text-rose-700">
                                        {{ $section['subtotal']['total'] != 0 ? 'Rp ' . number_format($section['subtotal']['total'], 0, ',', '.') : '—' }}
                                    </td>
                                </tr>

                                @foreach ($section['accounts'] as $account)
                                    @if (count($section['accounts']) === 1 && $account['nama'] === $section['name'])
                                        @continue
                                    @endif
                                    <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                        <td class="px-6 py-2 pl-14 text-slate-700 italic">
                                            {{ $account['nama'] }}
                                        </td>
                                        <td class="px-6 py-2 text-right font-normal italic tabular-nums whitespace-nowrap {{ $account['tidak_terikat'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                            {{ $account['tidak_terikat'] != 0 ? 'Rp ' . number_format($account['tidak_terikat'], 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="px-6 py-2 text-right font-normal italic tabular-nums whitespace-nowrap {{ $account['terikat'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                            {{ $account['terikat'] != 0 ? 'Rp ' . number_format($account['terikat'], 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="px-6 py-2 text-right font-normal italic tabular-nums whitespace-nowrap {{ $account['total'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                            {{ $account['total'] != 0 ? 'Rp ' . number_format($account['total'], 0, ',', '.') : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-3 text-center text-slate-400 italic">
                                        Tidak ada data beban manajemen umum pada periode ini.
                                    </td>
                                </tr>
                            @endforelse

                            <tr><td colspan="4" class="py-1"></td></tr>

                            <tr class="bg-red-100/60 border-t-2 border-red-300">
                                <td class="px-6 py-3 text-sm font-bold text-red-900 uppercase italic">
                                    Total Biaya
                                </td>
                                <td class="px-6 py-3 text-right font-bold italic tabular-nums whitespace-nowrap text-red-900">
                                    {{ $report['total_beban']['tidak_terikat'] != 0 ? 'Rp ' . number_format($report['total_beban']['tidak_terikat'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-3 text-right font-bold italic tabular-nums whitespace-nowrap text-red-900">
                                    {{ $report['total_beban']['terikat'] != 0 ? 'Rp ' . number_format($report['total_beban']['terikat'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-3 text-right font-bold italic tabular-nums whitespace-nowrap text-red-900">
                                    {{ $report['total_beban']['total'] != 0 ? 'Rp ' . number_format($report['total_beban']['total'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            <tr><td colspan="4" class="py-1"></td></tr>

                            @php
                                $surplusColor = $report['surplus']['total'] >= 0 
                                    ? 'bg-emerald-600 text-white' 
                                    : 'bg-red-600 text-white';
                            @endphp

                            <tr class="{{ $surplusColor }} border-t-2">
                                <td class="px-6 py-4 text-sm font-bold text-white uppercase italic">
                                    Total Pendapatan Komprehensif
                                </td>
                                <td class="px-6 py-4 text-right font-bold italic text-white tabular-nums whitespace-nowrap">
                                    @if($report['surplus']['tidak_terikat'] < 0)
                                        (Rp {{ number_format(abs($report['surplus']['tidak_terikat']), 0, ',', '.') }})
                                    @elseif($report['surplus']['tidak_terikat'] == 0)
                                        —
                                    @else
                                        Rp {{ number_format($report['surplus']['tidak_terikat'], 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-bold italic text-white tabular-nums whitespace-nowrap">
                                    @if($report['surplus']['terikat'] < 0)
                                        (Rp {{ number_format(abs($report['surplus']['terikat']), 0, ',', '.') }})
                                    @elseif($report['surplus']['terikat'] == 0)
                                        —
                                    @else
                                        Rp {{ number_format($report['surplus']['terikat'], 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-bold italic text-white tabular-nums whitespace-nowrap text-lg">
                                    @if($report['surplus']['total'] < 0)
                                        (Rp {{ number_format(abs($report['surplus']['total']), 0, ',', '.') }})
                                    @elseif($report['surplus']['total'] == 0)
                                        —
                                    @else
                                        Rp {{ number_format($report['surplus']['total'], 0, ',', '.') }}
                                    @endif
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                    <p class="text-xs text-slate-400">
                        Dicetak pada {{ now()->format('d/m/Y H:i') }} WIB
                    </p>
                    <p class="text-xs text-slate-400">
                        <span class="font-medium">Keterangan:</span>
                        Tidak Terikat = Transaksi tanpa Program Kerja &bull;
                        Terikat = Transaksi dengan Program Kerja
                    </p>
                </div>
            </div>
        </main>
    </x-layout.shell>
</x-app-layout>
