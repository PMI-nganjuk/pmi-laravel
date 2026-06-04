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
                :filters="['search' => '']"
            >
                <x-molecules.datatable-filters placeholder="Cari nomor dokumen, referensi, keterangan, akun COA, program, PIC...">
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
