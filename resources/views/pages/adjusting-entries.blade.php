<x-app-layout>
    <div
        class="adjusting-entries-page flex min-h-screen bg-slate-50 font-sans text-slate-900"
        x-cloak
        x-data="createAdjustingEntryPageComponent({
            storeUrl:                  @js(route('adjusting-entries.store')),
            updateBaseUrl:             @js(url('/adjusting-entries')),
            redirectUrl:               @js(route('adjusting-entries.index')),
            initialData: {
                transactionDate:        @js(old('transaction_date', date('Y-m-d'))),
                debitAccountId:         @js(old('debit_account_id', '')),
                creditAccountId:        @js(old('credit_account_id', '')),
                amount:                 @js(old('amount', '')),
                reference:              @js(old('reference', '')),
                description:            @js(old('description', '')),
                programId:              @js(old('program_id', '')),
                userId:                 @js(old('user_id', auth()->id())),
                journalEntryType:       @js(old('journal_entry_type', '')),
            },
            nextDocumentNumber:        @js($nextDocumentNumber),
            initialEditingId:          @js(old('_editing_id', ''))
        })"
        x-init="init()"
        x-on:click="handlePageClick($event)"
    >
        {{-- Mobile Sidebar Overlay --}}
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

        <x-layout.sidebar />

        <div class="flex-1 flex flex-col min-w-0">
            <x-layout.header page-title="Jurnal Penyesuaian" />

            <main class="p-6 space-y-6">

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

                @if ($errors->any())
                    <x-atoms.alert variant="danger" title="Form jurnal penyesuaian belum bisa disimpan.">
                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-atoms.alert>
                @endif

                {{-- Entry Form --}}
                <x-organisms.adjusting-entry-form
                    :all-accounts="$allAccounts"
                    :programs="$programs"
                    :users="$users"
                />

                {{-- Transaction History Table --}}
                <x-organisms.datatable-wrapper
                    :endpoint="route('adjusting-entries.index')"
                    default-sort="transaction_date"
                    default-dir="desc"
                    :filters="[
                        'search' => '',
                        'per_page' => '15',
                        'filter_tanggal' => '',
                        'filter_no_dokumen' => '',
                        'filter_program' => '',
                        'filter_referensi' => '',
                        'filter_coa' => '',
                        'filter_debit' => '',
                        'filter_kredit' => '',
                        'filter_keterangan' => '',
                        'filter_jenis_entri' => ''
                    ]"
                >
                    <x-molecules.datatable-filters placeholder="Cari nomor dokumen, keterangan...">
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
                                            <x-atoms.input type="text" x-model="filters.filter_coa" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" placeholder="Cari COA" />
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-content-base mb-1 block">Debit</label>
                                            <x-atoms.input type="text" x-model="filters.filter_debit" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" placeholder="Nominal Debit" />
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-content-base mb-1 block">Kredit</label>
                                            <x-atoms.input type="text" x-model="filters.filter_kredit" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" placeholder="Nominal Kredit" />
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-content-base mb-1 block">Entri Jurnal</label>
                                            <select x-model="filters.filter_jenis_entri" class="w-full text-sm border-surface-border rounded-lg bg-surface-base focus:ring-primary focus:border-primary">
                                                <option value="">Semua Entri</option>
                                                <option value="BEGINNING_BALANCES">Saldo Awal</option>
                                                <option value="ADJUSTMENT">Penyesuaian</option>
                                            </select>
                                        </div>
                                        <div class="col-span-1 md:col-span-2 lg:col-span-4">
                                            <label class="text-xs font-medium text-content-base mb-1 block">Keterangan</label>
                                            <x-atoms.input type="text" x-model="filters.filter_keterangan" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" placeholder="Cari Keterangan" />
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
                            :is-empty="$adjustingEntries->isEmpty()"
                            empty-title="Belum Ada Jurnal Penyesuaian"
                            empty-message="Tambahkan transaksi jurnal penyesuaian pertama menggunakan form di atas."
                            class="border-0 shadow-none"
                        >
                            <x-slot:headers>
                                <x-atoms.table-sort-head column="transaction_date" label="Tanggal" />
                                <x-atoms.table-sort-head column="document_number"  label="No. Dokumen" />
                                <x-atoms.table-head>Program Kerja</x-atoms.table-head>
                                <x-atoms.table-head>Referensi</x-atoms.table-head>
                                <x-atoms.table-head>COA Transaksi</x-atoms.table-head>
                                <x-atoms.table-head class="text-right">Debit</x-atoms.table-head>
                                <x-atoms.table-head class="text-right">Kredit</x-atoms.table-head>
                                <x-atoms.table-head>Keterangan</x-atoms.table-head>
                                <x-atoms.table-head>Entri Jurnal</x-atoms.table-head>
                                <x-atoms.table-head class="text-center">Aksi</x-atoms.table-head>
                            </x-slot:headers>

                            @foreach ($adjustingEntries as $entry)
                                @php
                                    $debitEntry  = $entry->generalLedgers->first(fn($gl) => (float) $gl->debit > 0);
                                    $creditEntry = $entry->generalLedgers->first(fn($gl) => (float) $gl->credit > 0);
                                    $amount      = $debitEntry ? (float) $debitEntry->debit : ($creditEntry ? (float) $creditEntry->credit : 0);

                                    $journalTypeVal = $debitEntry?->note;
                                    $journalTypeEnum = \App\Enums\JournalEntryTypeEnum::tryFrom($journalTypeVal);
                                    $journalTypeLabel = $journalTypeEnum ? $journalTypeEnum->label() : ($journalTypeVal ?: '—');
                                @endphp

                                {{-- Baris 1: Sisi Debit --}}
                                <x-atoms.table-row class="border-b-0 hover:bg-slate-50/40">
                                    <x-atoms.table-cell rowspan="2" class="whitespace-nowrap font-semibold text-content-base border-r border-slate-100">
                                        {{ $entry->transaction_date->format('d/m/Y') }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell rowspan="2" class="whitespace-nowrap font-bold text-content-base border-r border-slate-100">
                                        {{ $entry->document_number }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell rowspan="2" class="border-r border-slate-100">
                                        <span class="text-xs font-medium text-content-base">{{ $entry->program?->name ?? '—' }}</span>
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell rowspan="2" class="border-r border-slate-100">
                                        <span class="text-xs text-content-base">{{ $entry->reference ?? '—' }}</span>
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell class="border-r border-slate-100">
                                        <span class="text-xs text-content-muted font-medium">
                                            {{ $debitEntry?->chartOfAccount?->account_name ?? '—' }}
                                        </span>
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell class="text-right font-semibold text-content-base whitespace-nowrap border-r border-slate-100">
                                        Rp {{ number_format($amount, 0, ',', '.') }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell class="text-right text-content-muted border-r border-slate-100">
                                        -
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell rowspan="2" class="max-w-xs truncate text-content-muted border-r border-slate-100">
                                        {{ $entry->description ?? '—' }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell rowspan="2" class="border-r border-slate-100">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $debitEntry?->note === 'BEGINNING_BALANCES' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $journalTypeLabel }}
                                        </span>
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell rowspan="2" class="text-center">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <x-atoms.button
                                                type="button"
                                                variant="secondary"
                                                size="sm"
                                                data-adjusting-edit
                                                data-adjusting-id="{{ $entry->id }}"
                                                data-adjusting-date="{{ $entry->transaction_date->format('Y-m-d') }}"
                                                data-adjusting-debit-account="{{ $debitEntry?->chart_of_account_id }}"
                                                data-adjusting-credit-account="{{ $creditEntry?->chart_of_account_id }}"
                                                data-adjusting-amount="{{ (int) $amount }}"
                                                data-adjusting-reference="{{ $entry->reference }}"
                                                data-adjusting-description="{{ $entry->description }}"
                                                data-adjusting-program-id="{{ $entry->program_id }}"
                                                data-adjusting-user-id="{{ $entry->user_id }}"
                                                data-adjusting-document-number="{{ $entry->document_number }}"
                                                data-adjusting-journal-entry-type="{{ $debitEntry?->note }}"
                                                :aria-label="'Edit Jurnal Penyesuaian ' . $entry->document_number"
                                            >
                                                Edit
                                            </x-atoms.button>

                                            <form
                                                action="{{ route('adjusting-entries.destroy', $entry->id) }}"
                                                method="POST"
                                                x-on:submit="if(!confirm('Hapus transaksi jurnal penyesuaian {{ $entry->document_number }}?')) $event.preventDefault();"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <x-atoms.button type="submit" variant="danger" size="sm" :aria-label="'Hapus Jurnal Penyesuaian ' . $entry->document_number">
                                                    Hapus
                                                </x-atoms.button>
                                            </form>
                                        </div>
                                    </x-atoms.table-cell>
                                </x-atoms.table-row>

                                {{-- Baris 2: Sisi Kredit --}}
                                <x-atoms.table-row class="bg-slate-50/20 hover:bg-slate-50/40">
                                    <x-atoms.table-cell class="border-r border-slate-100">
                                        <span class="text-xs text-content-muted font-medium pl-6 block">
                                            {{ $creditEntry?->chartOfAccount?->account_name ?? '—' }}
                                        </span>
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell class="text-right text-content-muted border-r border-slate-100">
                                        -
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell class="text-right font-semibold text-content-base whitespace-nowrap border-r border-slate-100">
                                        Rp {{ number_format($amount, 0, ',', '.') }}
                                    </x-atoms.table-cell>
                                </x-atoms.table-row>
                            @endforeach
                        </x-organisms.table>

                        @if ($adjustingEntries->hasPages())
                            <div class="border-t border-surface-border bg-surface-base px-6 py-4">
                                {{ $adjustingEntries->links() }}
                            </div>
                        @endif
                    </div>
                </x-organisms.datatable-wrapper>

            </main>
        </div>
    </div>
</x-app-layout>
