<x-app-layout>
    <x-layout.shell page-title="Laporan Perubahan Aset Netto">
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
                                Laporan Perubahan Aset Netto
                            </h1>
                            <p class="text-sm text-slate-500 mt-1">
                                Periode:
                                <span class="font-semibold text-slate-700">
                                    Tahun {{ $report['year'] }}
                                </span>
                                (dibandingkan awal tahun {{ $report['previous_year'] }})
                            </p>
                        </div>

                        <form method="GET" action="{{ route('analysis-notes.index') }}"
                              class="flex items-end gap-3 flex-wrap print:hidden"
                              id="an-filter-form">
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
                            <a href="{{ route('analysis-notes.export', ['year' => $report['year']]) }}"
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

                {{-- ── BS Verification Badge ── --}}
                @php
                    $matches = $report['bs_verification']['matches'];
                @endphp
                <div class="px-6 py-3 border-b border-slate-100 print:hidden">
                    <div class="flex items-center gap-2">
                        @if($matches)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                </svg>
                                Rekonsiliasi OK — Aset Netto Akhir sesuai dengan Neraca
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                </svg>
                                Perhatian — Periksa rekonsiliasi dengan Neraca
                            </span>
                        @endif
                        <span class="text-xs text-slate-400">
                            Total Aset Netto Neraca:
                            <span class="font-semibold text-slate-600">
                                Rp {{ number_format($report['bs_verification']['total'], 0, ',', '.') }}
                            </span>
                        </span>
                    </div>
                </div>

                {{-- ── Table ── --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="an-report-table">
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

                            {{-- ════════════════════════════════════════ --}}
                            {{-- ASET NETTO TIDAK TERIKAT                 --}}
                            {{-- ════════════════════════════════════════ --}}
                            <tr class="bg-sky-50/60">
                                <td colspan="3" class="px-6 py-3 text-sm font-bold text-sky-900 uppercase tracking-wide">
                                    Aset Netto Tidak Terikat
                                </td>
                            </tr>

                            <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                <td class="px-6 py-2 pl-10 text-slate-700 italic">
                                    Saldo Awal
                                </td>
                                <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $report['tidak_terikat']['saldo_awal']['current'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                    {{ $report['tidak_terikat']['saldo_awal']['current'] != 0 ? 'Rp ' . number_format($report['tidak_terikat']['saldo_awal']['current'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $report['tidak_terikat']['saldo_awal']['previous'] != 0 ? 'text-slate-500' : 'text-slate-300' }}">
                                    {{ $report['tidak_terikat']['saldo_awal']['previous'] != 0 ? 'Rp ' . number_format($report['tidak_terikat']['saldo_awal']['previous'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                <td class="px-6 py-2 pl-10 text-slate-700 italic">
                                    Pendapatan Netto Tidak Terikat Periode Berjalan
                                </td>
                                <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $report['tidak_terikat']['pendapatan_netto']['current'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                    {{ $report['tidak_terikat']['pendapatan_netto']['current'] != 0 ? 'Rp ' . number_format($report['tidak_terikat']['pendapatan_netto']['current'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $report['tidak_terikat']['pendapatan_netto']['previous'] != 0 ? 'text-slate-500' : 'text-slate-300' }}">
                                    {{ $report['tidak_terikat']['pendapatan_netto']['previous'] != 0 ? 'Rp ' . number_format($report['tidak_terikat']['pendapatan_netto']['previous'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            <tr class="bg-sky-100/50 border-t border-sky-200">
                                <td class="px-6 py-2.5 pl-10 text-sm font-bold text-sky-800 italic">
                                    Saldo Akhir Aset Netto Tidak Terikat
                                </td>
                                <td class="px-6 py-2.5 text-right font-bold italic tabular-nums whitespace-nowrap text-sky-800">
                                    {{ $report['tidak_terikat']['saldo_akhir']['current'] != 0 ? 'Rp ' . number_format($report['tidak_terikat']['saldo_akhir']['current'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-2.5 text-right font-bold italic tabular-nums whitespace-nowrap text-sky-600">
                                    {{ $report['tidak_terikat']['saldo_akhir']['previous'] != 0 ? 'Rp ' . number_format($report['tidak_terikat']['saldo_akhir']['previous'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            <tr><td colspan="3" class="py-2"></td></tr>

                            {{-- ════════════════════════════════════════ --}}
                            {{-- ASET NETTO TERIKAT                       --}}
                            {{-- ════════════════════════════════════════ --}}
                            <tr class="bg-amber-50/60">
                                <td colspan="3" class="px-6 py-3 text-sm font-bold text-amber-900 uppercase tracking-wide">
                                    Aset Netto Terikat
                                </td>
                            </tr>

                            <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                <td class="px-6 py-2 pl-10 text-slate-700 italic">
                                    Saldo Awal
                                </td>
                                <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $report['terikat']['saldo_awal']['current'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                    {{ $report['terikat']['saldo_awal']['current'] != 0 ? 'Rp ' . number_format($report['terikat']['saldo_awal']['current'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $report['terikat']['saldo_awal']['previous'] != 0 ? 'text-slate-500' : 'text-slate-300' }}">
                                    {{ $report['terikat']['saldo_awal']['previous'] != 0 ? 'Rp ' . number_format($report['terikat']['saldo_awal']['previous'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                <td class="px-6 py-2 pl-10 text-slate-700 italic">
                                    Pendapatan Netto Terikat Periode Berjalan
                                </td>
                                <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $report['terikat']['pendapatan_netto']['current'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                    {{ $report['terikat']['pendapatan_netto']['current'] != 0 ? 'Rp ' . number_format($report['terikat']['pendapatan_netto']['current'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-2 text-right tabular-nums whitespace-nowrap {{ $report['terikat']['pendapatan_netto']['previous'] != 0 ? 'text-slate-500' : 'text-slate-300' }}">
                                    {{ $report['terikat']['pendapatan_netto']['previous'] != 0 ? 'Rp ' . number_format($report['terikat']['pendapatan_netto']['previous'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            <tr class="bg-amber-100/50 border-t border-amber-200">
                                <td class="px-6 py-2.5 pl-10 text-sm font-bold text-amber-800 italic">
                                    Saldo Akhir Aset Netto Terikat
                                </td>
                                <td class="px-6 py-2.5 text-right font-bold italic tabular-nums whitespace-nowrap text-amber-800">
                                    {{ $report['terikat']['saldo_akhir']['current'] != 0 ? 'Rp ' . number_format($report['terikat']['saldo_akhir']['current'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-2.5 text-right font-bold italic tabular-nums whitespace-nowrap text-amber-600">
                                    {{ $report['terikat']['saldo_akhir']['previous'] != 0 ? 'Rp ' . number_format($report['terikat']['saldo_akhir']['previous'], 0, ',', '.') : '—' }}
                                </td>
                            </tr>

                            <tr><td colspan="3" class="py-2"></td></tr>

                            {{-- ════════════════════════════════════════ --}}
                            {{-- TOTAL ASET NETTO                         --}}
                            {{-- ════════════════════════════════════════ --}}
                            <tr class="bg-slate-800 border-t-2 border-slate-600">
                                <td class="px-6 py-4 text-sm font-bold text-white uppercase italic">
                                    Total Aset Netto
                                </td>
                                <td class="px-6 py-4 text-right font-bold italic text-white tabular-nums whitespace-nowrap text-lg">
                                    {{ $report['total_aset_netto']['current'] != 0 ? 'Rp ' . number_format($report['total_aset_netto']['current'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold italic text-slate-300 tabular-nums whitespace-nowrap text-lg">
                                    {{ $report['total_aset_netto']['previous'] != 0 ? 'Rp ' . number_format($report['total_aset_netto']['previous'], 0, ',', '.') : '—' }}
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
                        Perubahan Aset Netto berasal dari kalkulasi Laporan Aktivitas •
                        Aset Netto Akhir harus konsisten dengan komponen Ekuitas di Neraca
                    </p>
                </div>

            </div>
        </main>
    </x-layout.shell>
</x-app-layout>
