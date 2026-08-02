<x-app-layout>
    <div
        class="receipts-page flex min-h-screen bg-slate-50 font-sans text-slate-900"
        x-cloak
        x-data="createCashReceiptPageComponent({
            storeUrl:                  @js(route('receipts.store')),
            updateBaseUrl:             @js(url('/receipts')),
            redirectUrl:               @js(route('receipts.index')),
            suggestDescriptionUrl:     @js(route('receipts.suggest-description')),
            transactionAccountOptions: @js($transactionAccounts->map(fn($c) => ['id' => $c->id, 'name' => $c->account_name])->values()),
            initialData: {
                transactionDate:        @js(old('transaction_date', date('Y-m-d'))),
                cashAccountCode:        @js(old('cash_account_code', '')) ,
                transactionAccountCode: @js(old('transaction_account_code', '')) ,
                amount:                 @js(old('amount', '')) ,
                reference:              @js(old('reference', '')) ,
                description:            @js(old('description', '')) ,
                programId:              @js(old('program_id', '')) ,
                userId:                 @js(old('user_id', auth()->id())) ,
            },
            nextDocumentNumber:        @js($nextDocumentNumber),
            initialEditingId:          @js(old('_editing_id', ''))
        })"
        x-init="init()"
        x-on:click="handlePageClick($event)"
    >
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

        <x-layout.sidebar />

        <div class="flex-1 flex flex-col min-w-0">
            <x-layout.header page-title="Penerimaan Kas" />

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
                    <x-atoms.alert variant="danger" title="Form penerimaan kas belum bisa disimpan.">
                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-atoms.alert>
                @endif

                {{-- Entry Form --}}
                <x-organisms.cash-receipt-form
                    :cash-accounts="$cashAccounts"
                    :transaction-accounts="$transactionAccounts"
                    :programs="$programs"
                    :users="$users"
                />

                {{-- Transaction History Table --}}
                <x-organisms.datatable-wrapper
                    :endpoint="route('receipts.index')"
                    default-sort="transaction_date"
                    default-dir="desc"
                    :filters="[
                        'search' => '',
                        'per_page' => '15',
                        'filter_tanggal' => '',
                        'filter_no_dokumen' => '',
                        'filter_program' => '',
                        'filter_pic' => '',
                        'filter_coa' => '',
                        'filter_nominal' => '',
                        'filter_keterangan' => ''
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
                                            <label class="text-xs font-medium text-content-base mb-1 block">Terima Dari</label>
                                            <select x-model="filters.filter_pic" class="w-full text-sm border-surface-border rounded-lg bg-surface-base focus:ring-primary focus:border-primary">
                                                <option value="">Semua User</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->name }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-content-base mb-1 block">Rekening Kas / Kode Transaksi</label>
                                            <x-atoms.input type="text" x-model="filters.filter_coa" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" placeholder="Cari COA" />
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-content-base mb-1 block">Nominal</label>
                                            <x-atoms.input type="text" x-model="filters.filter_nominal" @keydown.enter.prevent="fetchData(); showAdvancedFilter = false" class="w-full text-sm" placeholder="Nominal" />
                                        </div>
                                        <div class="col-span-1 md:col-span-2 lg:col-span-2">
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
                            :is-empty="$receipts->isEmpty()"
                            empty-title="Belum Ada Penerimaan Kas"
                            empty-message="Tambahkan transaksi penerimaan kas pertama menggunakan form di atas."
                            class="border-0 shadow-none"
                        >
                            <x-slot:headers>
                                <x-atoms.table-sort-head column="document_number"  label="No. Dokumen" />
                                <x-atoms.table-sort-head column="transaction_date" label="Tanggal" />
                                <x-atoms.table-head>Program Kerja</x-atoms.table-head>
                                <x-atoms.table-head>Terima Dari</x-atoms.table-head>
                                <x-atoms.table-head>Rekening Kas</x-atoms.table-head>
                                <x-atoms.table-head>Kode Transaksi</x-atoms.table-head>
                                <x-atoms.table-head class="text-right">Nominal</x-atoms.table-head>
                                <x-atoms.table-head>Keterangan</x-atoms.table-head>
                                <x-atoms.table-head class="text-center">Aksi</x-atoms.table-head>
                            </x-slot:headers>

                            @foreach ($receipts as $receipt)
                                @php
                                    // Cast to float because debit/credit are cast as decimal:2 (string type)
                                    $debitEntry  = $receipt->generalLedgers->first(fn($gl) => (float) $gl->debit > 0);
                                    $creditEntry = $receipt->generalLedgers->first(fn($gl) => (float) $gl->credit > 0);
                                    $amount      = $debitEntry ? (float) $debitEntry->debit : 0;
                                @endphp

                                <x-atoms.table-row>
                                    <x-atoms.table-cell class="whitespace-nowrap font-bold text-content-base">
                                        {{ $receipt->document_number }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell class="whitespace-nowrap">
                                        {{ $receipt->transaction_date->format('d/m/Y') }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell>
                                        <span class="text-xs font-medium text-content-base">{{ $receipt->program?->name ?? '—' }}</span>
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell>
                                        <span class="text-xs text-content-base">{{ $receipt->user?->name ?? '—' }}</span>
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell>
                                        <span class="text-xs text-content-muted">{{ $debitEntry?->chartOfAccount?->account_name ?? '—' }}</span>
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell>
                                        <span class="text-xs text-content-muted">{{ $creditEntry?->chartOfAccount?->account_name ?? '—' }}</span>
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell class="text-right font-semibold text-content-base whitespace-nowrap">
                                        Rp {{ number_format($amount, 0, ',', '.') }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell class="max-w-xs truncate text-content-muted">
                                        {{ $receipt->description ?? '—' }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell class="text-right">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <x-atoms.button
                                                type="button"
                                                variant="secondary"
                                                size="sm"
                                                data-receipt-edit
                                                data-receipt-id="{{ $receipt->id }}"
                                                data-receipt-date="{{ $receipt->transaction_date->format('Y-m-d') }}"
                                                data-receipt-cash-account="{{ $debitEntry?->chart_of_account_id }}"
                                                data-receipt-transaction-account="{{ $creditEntry?->chart_of_account_id }}"
                                                data-receipt-amount="{{ (int) $amount }}"
                                                data-receipt-reference="{{ $receipt->reference }}"
                                                data-receipt-description="{{ $receipt->description }}"
                                                data-receipt-program-id="{{ $receipt->program_id }}"
                                                data-receipt-user-id="{{ $receipt->user_id }}"
                                                data-receipt-document-number="{{ $receipt->document_number }}"
                                                :aria-label="'Edit Penerimaan Kas ' . $receipt->document_number"
                                            >
                                                Edit
                                            </x-atoms.button>

                                            <form
                                                action="{{ route('receipts.destroy', $receipt->id) }}"
                                                method="POST"
                                                x-on:submit="if(!confirm('Hapus transaksi penerimaan kas {{ $receipt->document_number }}?')) $event.preventDefault();"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <x-atoms.button type="submit" variant="danger" size="sm" :aria-label="'Hapus Penerimaan Kas ' . $receipt->document_number">
                                                    Hapus
                                                </x-atoms.button>
                                            </form>
                                        </div>
                                    </x-atoms.table-cell>
                                </x-atoms.table-row>
                            @endforeach
                        </x-organisms.table>

                        @if ($receipts->hasPages())
                            <div class="border-t border-surface-border bg-surface-base px-6 py-4">
                                {{ $receipts->links() }}
                            </div>
                        @endif
                    </div>
                </x-organisms.datatable-wrapper>

            </main>
        </div>
    </div>
</x-app-layout>
