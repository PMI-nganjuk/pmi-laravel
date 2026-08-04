<x-app-layout>
    <x-layout.shell page-title="Laporan Posisi Keuangan">
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

                {{-- ── Header & Filter ── --}}
                <div class="px-6 py-5 border-b border-slate-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                                Laporan Posisi Keuangan
                            </h1>
                            <p class="text-sm text-slate-500 mt-1">
                                Periode: 
                                <span class="font-semibold text-slate-700">
                                    Tahun {{ $report['year'] }}
                                </span>
                                (dibandingkan dengan Tahun {{ $report['previous_year'] }})
                            </p>
                        </div>

                        <form method="GET" action="{{ route('balance-sheet.index') }}"
                              class="flex items-end gap-3 flex-wrap print:hidden"
                              id="bs-filter-form">
                            <div class="flex flex-col">
                                <label for="year" class="text-xs font-medium text-slate-500 mb-1">Tahun</label>
                                <select name="year" id="year"
                                        onchange="this.form.submit()"
                                        class="px-3 py-2 pr-10 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-sky-500/20 focus:border-sky-400 transition-colors bg-white">
                                    @for ($y = $current_year; $y >= $current_year - 5; $y--)
                                        <option value="{{ $y }}" {{ $report['year'] == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <a href="{{ route('balance-sheet.export', ['year' => $report['year']]) }}"
                               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200 flex items-center gap-1.5 self-end">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Ekspor Excel
                            </a>
                        </form>
                    </div>
                </div>

                {{-- ── Table ── --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="bs-report-table">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="text-left px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500 w-[55%]">
                                    Uraian
                                </th>
                                <th class="text-right px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500 w-[22%]">
                                    {{ $report['year'] }}
                                </th>
                                <th class="text-right px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500 w-[23%]">
                                    {{ $report['previous_year'] }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">

                            {{-- ════════════════════════════════ --}}
                            {{-- ASET                            --}}
                            {{-- ════════════════════════════════ --}}
                            <tr class="bg-sky-50/60">
                                <td colspan="3" class="px-6 py-3 text-sm font-bold text-sky-900 uppercase tracking-wide">
                                    Aset
                                </td>
                            </tr>

                            {{-- Aset Lancar --}}
                            <tr class="bg-sky-50/30">
                                <td colspan="3" class="px-6 py-2.5 pl-10 text-sm font-bold text-sky-700">
                                    Aset Lancar
                                </td>
                            </tr>

                            @foreach ($report['aset_lancar'] as $row)
                                <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                    <td class="px-6 py-2 pl-14 text-slate-700 italic">
                                        {{ $row['name'] }}
                                    </td>
                                    <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $row['current'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                        {{ $row['current'] != 0 ? 'Rp ' . number_format($row['current'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $row['previous'] != 0 ? 'text-slate-500' : 'text-slate-300' }}">
                                        {{ $row['previous'] != 0 ? 'Rp ' . number_format($row['previous'], 0, ',', '.') : '—' }}
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="bg-sky-100/50 border-t border-sky-200">
                                <td class="px-6 py-2.5 pl-10 text-sm font-bold text-sky-800 italic">
                                    Total Aset Lancar
                                </td>
                                <td class="px-6 py-2.5 text-right font-bold italic tabular-nums whitespace-nowrap text-sky-800">
                                    {{ $report['total_aset_lancar']['current'] != 0 ? 'Rp ' . number_format($report['total_aset_lancar']['current'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-2.5 text-right font-bold italic tabular-nums whitespace-nowrap text-sky-600">
                                    {{ $report['total_aset_lancar']['previous'] != 0 ? 'Rp ' . number_format($report['total_aset_lancar']['previous'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            <tr><td colspan="3" class="py-1"></td></tr>

                            {{-- Aset Tidak Lancar --}}
                            <tr class="bg-sky-50/30">
                                <td colspan="3" class="px-6 py-2.5 pl-10 text-sm font-bold text-sky-700">
                                    Aset Tidak Lancar
                                </td>
                            </tr>

                            @foreach ($report['aset_tidak_lancar'] as $row)
                                <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                    <td class="px-6 py-2 pl-14 text-slate-700 italic">
                                        {{ $row['name'] }}
                                    </td>
                                    <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $row['current'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                        {{ $row['current'] != 0 ? 'Rp ' . number_format($row['current'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $row['previous'] != 0 ? 'text-slate-500' : 'text-slate-300' }}">
                                        {{ $row['previous'] != 0 ? 'Rp ' . number_format($row['previous'], 0, ',', '.') : '—' }}
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="bg-sky-100/50 border-t border-sky-200">
                                <td class="px-6 py-2.5 pl-10 text-sm font-bold text-sky-800 italic">
                                    Total Aset Tidak Lancar
                                </td>
                                <td class="px-6 py-2.5 text-right font-bold italic tabular-nums whitespace-nowrap text-sky-800">
                                    {{ $report['total_aset_tidak_lancar']['current'] != 0 ? 'Rp ' . number_format($report['total_aset_tidak_lancar']['current'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-2.5 text-right font-bold italic tabular-nums whitespace-nowrap text-sky-600">
                                    {{ $report['total_aset_tidak_lancar']['previous'] != 0 ? 'Rp ' . number_format($report['total_aset_tidak_lancar']['previous'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            {{-- Total Aset --}}
                            <tr class="bg-sky-200/60 border-t-2 border-sky-400">
                                <td class="px-6 py-3 text-sm font-bold text-sky-900 uppercase italic">
                                    Total Assets
                                </td>
                                <td class="px-6 py-3 text-right font-bold italic tabular-nums whitespace-nowrap text-sky-900 text-base">
                                    {{ $report['total_aset']['current'] != 0 ? 'Rp ' . number_format($report['total_aset']['current'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-3 text-right font-bold italic tabular-nums whitespace-nowrap text-sky-700 text-base">
                                    {{ $report['total_aset']['previous'] != 0 ? 'Rp ' . number_format($report['total_aset']['previous'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            <tr><td colspan="3" class="py-2"></td></tr>

                            {{-- ════════════════════════════════ --}}
                            {{-- LIABILITAS                      --}}
                            {{-- ════════════════════════════════ --}}
                            <tr class="bg-amber-50/60">
                                <td colspan="3" class="px-6 py-3 text-sm font-bold text-amber-900 uppercase tracking-wide">
                                    Liabilitas
                                </td>
                            </tr>

                            {{-- Liabilitas Lancar --}}
                            <tr class="bg-amber-50/30">
                                <td colspan="3" class="px-6 py-2.5 pl-10 text-sm font-bold text-amber-700">
                                    Liabilitas Lancar
                                </td>
                            </tr>

                            @foreach ($report['liabilitas_lancar'] as $row)
                                <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                    <td class="px-6 py-2 pl-14 text-slate-700 italic">
                                        {{ $row['name'] }}
                                    </td>
                                    <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $row['current'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                        {{ $row['current'] != 0 ? 'Rp ' . number_format($row['current'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $row['previous'] != 0 ? 'text-slate-500' : 'text-slate-300' }}">
                                        {{ $row['previous'] != 0 ? 'Rp ' . number_format($row['previous'], 0, ',', '.') : '—' }}
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="bg-amber-100/50 border-t border-amber-200">
                                <td class="px-6 py-2.5 pl-10 text-sm font-bold text-amber-800 italic">
                                    Total Liabilitas Lancar
                                </td>
                                <td class="px-6 py-2.5 text-right font-bold italic tabular-nums whitespace-nowrap text-amber-800">
                                    {{ $report['total_liabilitas_lancar']['current'] != 0 ? 'Rp ' . number_format($report['total_liabilitas_lancar']['current'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-2.5 text-right font-bold italic tabular-nums whitespace-nowrap text-amber-600">
                                    {{ $report['total_liabilitas_lancar']['previous'] != 0 ? 'Rp ' . number_format($report['total_liabilitas_lancar']['previous'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            <tr><td colspan="3" class="py-1"></td></tr>

                            {{-- Liabilitas Tidak Lancar --}}
                            <tr class="bg-amber-50/30">
                                <td colspan="3" class="px-6 py-2.5 pl-10 text-sm font-bold text-amber-700">
                                    Liabilitas Tidak Lancar
                                </td>
                            </tr>

                            @foreach ($report['liabilitas_tidak_lancar'] as $row)
                                <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                    <td class="px-6 py-2 pl-14 text-slate-700 italic">
                                        {{ $row['name'] }}
                                    </td>
                                    <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $row['current'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                        {{ $row['current'] != 0 ? 'Rp ' . number_format($row['current'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $row['previous'] != 0 ? 'text-slate-500' : 'text-slate-300' }}">
                                        {{ $row['previous'] != 0 ? 'Rp ' . number_format($row['previous'], 0, ',', '.') : '—' }}
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="bg-amber-100/50 border-t border-amber-200">
                                <td class="px-6 py-2.5 pl-10 text-sm font-bold text-amber-800 italic">
                                    Total Liabilitas Tidak Lancar
                                </td>
                                <td class="px-6 py-2.5 text-right font-bold italic tabular-nums whitespace-nowrap text-amber-800">
                                    {{ $report['total_liabilitas_tidak_lancar']['current'] != 0 ? 'Rp ' . number_format($report['total_liabilitas_tidak_lancar']['current'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-2.5 text-right font-bold italic tabular-nums whitespace-nowrap text-amber-600">
                                    {{ $report['total_liabilitas_tidak_lancar']['previous'] != 0 ? 'Rp ' . number_format($report['total_liabilitas_tidak_lancar']['previous'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            <tr><td colspan="3" class="py-2"></td></tr>

                            {{-- ════════════════════════════════ --}}
                            {{-- ASET NETTO                      --}}
                            {{-- ════════════════════════════════ --}}
                            <tr class="bg-emerald-50/60">
                                <td colspan="3" class="px-6 py-3 text-sm font-bold text-emerald-900 uppercase tracking-wide">
                                    Aset Netto
                                </td>
                            </tr>

                            @foreach ($report['aset_netto'] as $row)
                                <tr class="hover:bg-slate-50/50 transition-colors duration-150 {{ $row['is_pl'] ? 'bg-emerald-50/20' : '' }}">
                                    <td class="px-6 py-2 pl-10 text-slate-700 {{ $row['is_pl'] ? 'italic font-medium' : 'italic' }}">
                                        {{ $row['name'] }}
                                    </td>
                                    <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $row['current'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                        {{ $row['current'] != 0 ? 'Rp ' . number_format($row['current'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $row['previous'] != 0 ? 'text-slate-500' : 'text-slate-300' }}">
                                        {{ $row['previous'] != 0 ? 'Rp ' . number_format($row['previous'], 0, ',', '.') : '—' }}
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="bg-emerald-100/60 border-t border-emerald-300">
                                <td class="px-6 py-2.5 pl-10 text-sm font-bold text-emerald-800 italic">
                                    Total Asset Netto
                                </td>
                                <td class="px-6 py-2.5 text-right font-bold italic tabular-nums whitespace-nowrap text-emerald-800">
                                    {{ $report['total_aset_netto']['current'] != 0 ? 'Rp ' . number_format($report['total_aset_netto']['current'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-2.5 text-right font-bold italic tabular-nums whitespace-nowrap text-emerald-600">
                                    {{ $report['total_aset_netto']['previous'] != 0 ? 'Rp ' . number_format($report['total_aset_netto']['previous'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            <tr><td colspan="3" class="py-1"></td></tr>

                            {{-- ════════════════════════════════ --}}
                            {{-- TOTAL LIABILITAS + ASET NETTO   --}}
                            {{-- ════════════════════════════════ --}}
                            <tr class="bg-slate-800 border-t-2 border-slate-600">
                                <td class="px-6 py-4 text-sm font-bold text-white uppercase italic">
                                    Total Liabilitas dan Aset Netto
                                </td>
                                <td class="px-6 py-4 text-right font-bold italic text-white tabular-nums whitespace-nowrap text-lg">
                                    {{ $report['total_liabilitas_dan_aset_netto']['current'] != 0 ? 'Rp ' . number_format($report['total_liabilitas_dan_aset_netto']['current'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold italic text-slate-300 tabular-nums whitespace-nowrap text-lg">
                                    {{ $report['total_liabilitas_dan_aset_netto']['previous'] != 0 ? 'Rp ' . number_format($report['total_liabilitas_dan_aset_netto']['previous'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                    <p class="text-xs text-slate-400">
                        Dicetak pada {{ now()->format('d/m/Y H:i') }} WIB
                    </p>
                    <p class="text-xs text-slate-400">
                        <span class="font-medium">Keterangan:</span>
                        Pendapatan Netto Periode Berjalan diambil dari kalkulasi Laporan Aktivitas
                    </p>
                </div>

            </div>
        </main>
    </x-layout.shell>
</x-app-layout>