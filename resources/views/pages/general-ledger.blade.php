<x-app-layout>
    <x-layout.shell page-title="Buku Besar (General Ledger)">
        <main class="flex-1 overflow-y-auto p-6 space-y-6">
            {{-- Session Alerts --}}
            @if (session('success'))
                <x-atoms.alert variant="success">
                    {{ session('success') }}
                </x-atoms.alert>
            @endif

            @if (session('error'))
                <x-atoms.alert variant="danger">
                    {{ session('error') }}
                </x-atoms.alert>
            @endif

            {{-- General Ledger Table Wrapper --}}
            <x-organisms.datatable-wrapper
                :endpoint="route('general-ledger.index')"
                default-sort="transaction_date"
                default-dir="desc"
                :filters="[
                    'search' => '',
                    'per_page' => '25',
                    'filter_tanggal' => '',
                    'filter_no_dokumen' => '',
                    'filter_program' => '',
                    'filter_referensi' => '',
                    'filter_coa' => '',
                    'filter_debit' => '',
                    'filter_kredit' => '',
                    'filter_keterangan' => '',
                    'filter_pic' => '',
                    'filter_bs' => '',
                    'filter_pl' => '',
                    'filter_note' => '',
                ]"
            >
                <x-molecules.datatable-filters placeholder="Cari nomor dokumen, referensi, keterangan, akun COA, program, PIC...">
                    <div x-data="{ showAdvancedFilter: false }" class="flex items-center gap-2 relative">
                        <x-atoms.button type="button" variant="outline" size="md" @click="showAdvancedFilter = !showAdvancedFilter">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </x-atoms.button>

                        <!-- Modal Overlay -->
                        <div x-show="showAdvancedFilter" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center">
                            <!-- Backdrop -->
                            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"
                                 @click="showAdvancedFilter = false"
                                 x-transition.opacity>
                            </div>

                            <!-- Modal Content -->
                            <div class="relative bg-surface-base border border-surface-border rounded-xl shadow-2xl w-[64rem] max-w-[95vw] p-6 flex flex-col gap-6"
                                 x-transition.scale.origin.center>
                                <h4 class="font-bold text-lg text-content-base border-b border-surface-border pb-3">Filter Lanjutan</h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <label class="text-xs font-medium text-content-base mb-1 block">Tanggal</label>
                                        <x-atoms.input type="date" x-model="filters.filter_tanggal" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" />
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-content-base mb-1 block">No. Dokumen</label>
                                        <x-atoms.input type="text" x-model="filters.filter_no_dokumen" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" placeholder="Cari No. Dokumen" />
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-content-base mb-1 block">Program Kerja</label>
                                        <select x-model="filters.filter_program" class="w-full text-sm border-surface-border rounded-lg bg-surface-base focus:ring-primary focus:border-primary">
                                            <option value="">Semua Program</option>
                                            @foreach($programs as $program)
                                                <option value="{{ $program->name }}">{{ $program->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-content-base mb-1 block">Referensi</label>
                                        <x-atoms.input type="text" x-model="filters.filter_referensi" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" placeholder="Cari Referensi" />
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-content-base mb-1 block">COA Transaksi</label>
                                        <select x-model="filters.filter_coa" class="w-full text-sm border-surface-border rounded-lg bg-surface-base focus:ring-primary focus:border-primary">
                                            <option value="">Semua COA</option>
                                            @foreach($coas as $coa)
                                                <option value="{{ $coa->account_name }}">{{ $coa->id }} - {{ $coa->account_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-content-base mb-1 block">Pihak Terkait / PIC</label>
                                        <select x-model="filters.filter_pic" class="w-full text-sm border-surface-border rounded-lg bg-surface-base focus:ring-primary focus:border-primary">
                                            <option value="">Semua PIC</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->name }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-content-base mb-1 block">Debit</label>
                                        <x-atoms.input type="text" x-model="filters.filter_debit" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" placeholder="Nominal Debit" />
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-content-base mb-1 block">Kredit</label>
                                        <x-atoms.input type="text" x-model="filters.filter_kredit" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" placeholder="Nominal Kredit" />
                                    </div>
                                    <div class="col-span-1 md:col-span-2 lg:col-span-2">
                                        <label class="text-xs font-medium text-content-base mb-1 block">Keterangan</label>
                                        <x-atoms.input type="text" x-model="filters.filter_keterangan" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" placeholder="Cari Keterangan" />
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-content-base mb-1 block">Dampak Neraca (BS)</label>
                                        <x-atoms.input type="text" x-model="filters.filter_bs" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" placeholder="Nominal BS" />
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-content-base mb-1 block">Dampak Laba Rugi (PL)</label>
                                        <x-atoms.input type="text" x-model="filters.filter_pl" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" placeholder="Nominal PL" />
                                    </div>
                                    <div class="col-span-1 md:col-span-2 lg:col-span-4">
                                        <label class="text-xs font-medium text-content-base mb-1 block">Jenis Entri / Note</label>
                                        <x-atoms.input type="text" x-model="filters.filter_note" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" placeholder="Cari Note" />
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 mt-2 pt-4 border-t border-surface-border">
                                    <x-atoms.button type="button" variant="outline" size="sm" @click="showAdvancedFilter = false">
                                        Batal
                                    </x-atoms.button>
                                    <x-atoms.button type="button" variant="info" size="sm" @click="fetchData(); showAdvancedFilter = false">
                                        Cari
                                    </x-atoms.button>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 ml-4 border-l border-surface-border pl-4">
                            <label for="per_page" class="text-sm font-medium text-content-base whitespace-nowrap">Tampilkan:</label>
                            <select id="per_page" x-model="filters.per_page" @change="fetchData()" class="text-sm border-surface-border rounded-lg bg-surface-base focus:ring-primary focus:border-primary">
                                <option value="5">5 data</option>
                                <option value="10">10 data</option>
                                <option value="15">15 data</option>
                                <option value="25">25 data</option>
                                <option value="-1">Semua Data</option>
                            </select>
                        </div>
                    </div>
                </x-molecules.datatable-filters>

                <div id="data-container">
                    <x-organisms.table
                        :is-empty="$entries->isEmpty()"
                        empty-title="Belum Ada Jurnal Buku Besar"
                        empty-message="Tidak ditemukan entri jurnal buku besar untuk filter periode keuangan atau pencarian saat ini."
                        class="border-0 shadow-none"
                    >
                        <x-slot:headers>
                            <x-atoms.table-sort-head column="transaction_date" label="Tanggal" />
                            <x-atoms.table-sort-head column="document_number" label="No. Dokumen" />
                            <x-atoms.table-head>Program Kerja</x-atoms.table-head>
                            <x-atoms.table-head>Referensi</x-atoms.table-head>
                            <x-atoms.table-head>COA Transaksi</x-atoms.table-head>
                            <x-atoms.table-head class="text-right">Debit</x-atoms.table-head>
                            <x-atoms.table-head class="text-right">Kredit</x-atoms.table-head>
                            <x-atoms.table-head>Keterangan</x-atoms.table-head>
                            <x-atoms.table-head>Pihak Terkait / PIC</x-atoms.table-head>
                            <x-atoms.table-head class="text-right">Dampak Neraca (BS)</x-atoms.table-head>
                            <x-atoms.table-head class="text-right">Dampak Laba Rugi (PL)</x-atoms.table-head>
                            <x-atoms.table-head>Jenis Entri / Note</x-atoms.table-head>
                        </x-slot:headers>

                        @foreach ($entries as $entry)
                            @php
                                $debit = (float) $entry->debit;
                                $credit = (float) $entry->credit;
                                $bsImpact = $debit - $credit;
                                $plImpact = $credit - $debit;
                                
                                $reference = $entry->transaction?->reference ?: $entry->transaction?->transaction_type?->label();
                            @endphp

                            <x-atoms.table-row>
                                <x-atoms.table-cell class="whitespace-nowrap">
                                    {{ $entry->transaction?->transaction_date?->format('d/m/Y') }}
                                </x-atoms.table-cell>

                                <x-atoms.table-cell class="whitespace-nowrap font-bold text-content-base">
                                    {{ $entry->transaction?->document_number }}
                                </x-atoms.table-cell>

                                <x-atoms.table-cell>
                                    <span class="text-xs font-medium text-content-base">
                                        {{ $entry->transaction?->program?->name ?? '—' }}
                                    </span>
                                </x-atoms.table-cell>

                                <x-atoms.table-cell>
                                    <span class="text-xs text-content-base">
                                        {{ $reference }}
                                    </span>
                                </x-atoms.table-cell>

                                <x-atoms.table-cell class="whitespace-nowrap">
                                    <span class="text-xs text-content-muted">
                                        {{ $entry->chartOfAccount?->account_name }}
                                    </span>
                                </x-atoms.table-cell>

                                <x-atoms.table-cell class="text-right font-medium whitespace-nowrap">
                                    {{ $debit > 0 ? 'Rp ' . number_format($debit, 0, ',', '.') : '—' }}
                                </x-atoms.table-cell>

                                <x-atoms.table-cell class="text-right font-medium whitespace-nowrap">
                                    {{ $credit > 0 ? 'Rp ' . number_format($credit, 0, ',', '.') : '—' }}
                                </x-atoms.table-cell>

                                <x-atoms.table-cell class="max-w-xs truncate text-content-muted" title="{{ $entry->transaction?->description }}">
                                    {{ $entry->transaction?->description ?? '—' }}
                                </x-atoms.table-cell>

                                <x-atoms.table-cell>
                                    <span class="text-xs text-content-base">
                                        {{ $entry->transaction?->user?->name ?? '—' }}
                                    </span>
                                </x-atoms.table-cell>

                                <x-atoms.table-cell class="text-right font-semibold whitespace-nowrap">
                                    @if($bsImpact < 0)
                                        <span class="text-red-600">-Rp {{ number_format(abs($bsImpact), 0, ',', '.') }}</span>
                                    @elseif($bsImpact > 0)
                                        <span class="text-emerald-600">Rp {{ number_format($bsImpact, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-content-muted">Rp 0</span>
                                    @endif
                                </x-atoms.table-cell>

                                <x-atoms.table-cell class="text-right font-semibold whitespace-nowrap">
                                    @if($plImpact < 0)
                                        <span class="text-red-600">-Rp {{ number_format(abs($plImpact), 0, ',', '.') }}</span>
                                    @elseif($plImpact > 0)
                                        <span class="text-emerald-600">Rp {{ number_format($plImpact, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-content-muted">Rp 0</span>
                                    @endif
                                </x-atoms.table-cell>

                                <x-atoms.table-cell>
                                    <span class="text-xs text-content-muted">
                                        {{ $entry->note ?? '—' }}
                                    </span>
                                </x-atoms.table-cell>
                            </x-atoms.table-row>
                        @endforeach
                    </x-organisms.table>

                    @if ($entries->hasPages())
                        <div class="border-t border-surface-border bg-surface-base px-6 py-4">
                            {{ $entries->links() }}
                        </div>
                    @endif
                </div>
            </x-organisms.datatable-wrapper>
        </main>
    </x-layout.shell>
</x-app-layout>
