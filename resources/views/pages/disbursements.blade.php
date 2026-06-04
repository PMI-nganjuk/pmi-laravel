<x-app-layout>
    <div
        class="disbursements-page flex min-h-screen bg-slate-50 font-sans text-slate-900"
        x-cloak
        x-data="createCashDisbursementPageComponent({
            storeUrl:                  @js(route('disbursements.store')),
            updateBaseUrl:             @js(url('/disbursements')),
            redirectUrl:               @js(route('disbursements.index')),
            suggestDescriptionUrl:     @js(route('disbursements.suggest-description')),
            transactionAccountOptions: @js($transactionAccounts->map(fn($c) => ['id' => $c->id, 'name' => $c->account_name])->values()),
            initialData: {
                transactionDate:        @js(old('transaction_date', date('Y-m-d'))),
                cashAccountCode:        @js(old('cash_account_code', '')),
                transactionAccountCode: @js(old('transaction_account_code', '')),
                amount:                 @js(old('amount', '')),
                reference:              @js(old('reference', '')),
                description:            @js(old('description', '')),
                programId:              @js(old('program_id', '')),
                userId:                 @js(old('user_id', auth()->id())),
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
            <x-layout.header page-title="Pengeluaran Kas" />

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
                    <x-atoms.alert variant="danger" title="Form pengeluaran kas belum bisa disimpan.">
                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-atoms.alert>
                @endif

                {{-- Entry Form --}}
                <x-organisms.cash-disbursement-form
                    :cash-accounts="$cashAccounts"
                    :transaction-accounts="$transactionAccounts"
                    :programs="$programs"
                    :users="$users"
                />

                {{-- Transaction History Table --}}
                <x-organisms.datatable-wrapper
                    :endpoint="route('disbursements.index')"
                    default-sort="transaction_date"
                    default-dir="desc"
                    :filters="['search' => '']"
                >
                    <x-molecules.datatable-filters placeholder="Cari nomor dokumen, keterangan...">
                    </x-molecules.datatable-filters>

                    <div id="data-container">
                        <x-organisms.table
                            :is-empty="$disbursements->isEmpty()"
                            empty-title="Belum Ada Pengeluaran Kas"
                            empty-message="Tambahkan transaksi pengeluaran kas pertama menggunakan form di atas."
                            class="border-0 shadow-none"
                        >
                            <x-slot:headers>
                                <x-atoms.table-sort-head column="document_number"  label="No. Dokumen" />
                                <x-atoms.table-sort-head column="transaction_date" label="Tanggal" />
                                <x-atoms.table-head>Program Kerja</x-atoms.table-head>
                                <x-atoms.table-head>Dibayarkan Kepada</x-atoms.table-head>
                                <x-atoms.table-head>Kode Transaksi</x-atoms.table-head>
                                <x-atoms.table-head>Rekening Kas</x-atoms.table-head>
                                <x-atoms.table-head class="text-right">Nominal</x-atoms.table-head>
                                <x-atoms.table-head>Keterangan</x-atoms.table-head>
                                <x-atoms.table-head class="text-center">Aksi</x-atoms.table-head>
                            </x-slot:headers>

                            @foreach ($disbursements as $disbursement)
                                @php
                    // GL: Debit → Kode Transaksi, Credit → Rekening Kas
                    // Cast to float because debit/credit are cast as decimal:2 (string type)
                    $debitEntry  = $disbursement->generalLedgers->first(fn($gl) => (float) $gl->debit > 0);
                    $creditEntry = $disbursement->generalLedgers->first(fn($gl) => (float) $gl->credit > 0);
                    $amount      = $debitEntry ? (float) $debitEntry->debit : 0;
                @endphp

                                <x-atoms.table-row>
                                    <x-atoms.table-cell class="whitespace-nowrap font-bold text-content-base">
                                        {{ $disbursement->document_number }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell class="whitespace-nowrap">
                                        {{ $disbursement->transaction_date->format('d/m/Y') }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell>
                                        <span class="text-xs font-medium text-content-base">{{ $disbursement->program?->name ?? '—' }}</span>
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell>
                                        <span class="text-xs text-content-base">{{ $disbursement->user?->name ?? '—' }}</span>
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
                                        {{ $disbursement->description ?? '—' }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell class="text-right">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <x-atoms.button
                                                type="button"
                                                variant="secondary"
                                                size="sm"
                                                data-disbursement-edit
                                                data-disbursement-id="{{ $disbursement->id }}"
                                                data-disbursement-date="{{ $disbursement->transaction_date->format('Y-m-d') }}"
                                                data-disbursement-cash-account="{{ $creditEntry?->chart_of_account_id }}"
                                                data-disbursement-transaction-account="{{ $debitEntry?->chart_of_account_id }}"
                                                data-disbursement-amount="{{ (int) $amount }}"
                                                data-disbursement-reference="{{ $disbursement->reference }}"
                                                data-disbursement-description="{{ $disbursement->description }}"
                                                data-disbursement-program-id="{{ $disbursement->program_id }}"
                                                data-disbursement-user-id="{{ $disbursement->user_id }}"
                                                data-disbursement-document-number="{{ $disbursement->document_number }}"
                                                :aria-label="'Edit Pengeluaran Kas ' . $disbursement->document_number"
                                            >
                                                Edit
                                            </x-atoms.button>

                                            <form
                                                action="{{ route('disbursements.destroy', $disbursement->id) }}"
                                                method="POST"
                                                x-on:submit="if(!confirm('Hapus transaksi pengeluaran kas {{ $disbursement->document_number }}?')) $event.preventDefault();"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <x-atoms.button type="submit" variant="danger" size="sm" :aria-label="'Hapus Pengeluaran Kas ' . $disbursement->document_number">
                                                    Hapus
                                                </x-atoms.button>
                                            </form>
                                        </div>
                                    </x-atoms.table-cell>
                                </x-atoms.table-row>
                            @endforeach
                        </x-organisms.table>

                        @if ($disbursements->hasPages())
                            <div class="border-t border-surface-border bg-surface-base px-6 py-4">
                                {{ $disbursements->links() }}
                            </div>
                        @endif
                    </div>
                </x-organisms.datatable-wrapper>

            </main>
        </div>
    </div>
</x-app-layout>
