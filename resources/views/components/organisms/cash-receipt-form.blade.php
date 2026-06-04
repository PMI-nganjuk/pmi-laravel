@props([
    'cashAccounts'        => collect(),
    'transactionAccounts' => collect(),
    'programs'            => collect(),
    'users'               => collect(),
])

<x-atoms.surface
    tag="section"
    aria-labelledby="receipt-form-title"
    x-cloak
    x-transition
>
    <x-molecules.form-header
        id="receipt-form-title"
        badgeXText="editingId ? 'Mode edit' : 'Input Baru'"
        titleXText="editingId ? 'Edit Penerimaan Kas' : 'Tambah Penerimaan Kas'"
        subtitleXText="editingId ? 'Perbarui detail transaksi kas masuk.' : 'Isi detail transaksi kas masuk. Keterangan dapat diisi otomatis berdasarkan kode transaksi yang dipilih.'"
    />

    <form
        x-ref="receiptForm"
        x-bind:action="formAction"
        method="POST"
        x-on:submit.prevent="submitForm()"
        class="grid grid-cols-1 gap-5 md:grid-cols-2"
        novalidate
    >
        @csrf

        <template x-if="editingId">
            <input type="hidden" name="_method" value="PUT">
        </template>

        <input type="hidden" name="_editing_id" x-model="editingId">

        {{-- Tanggal Transaksi --}}
        <x-atoms.input
            name="transaction_date"
            type="date"
            label="Tanggal Transaksi"
            x-model="transactionDate"
            required
            :value="old('transaction_date', date('Y-m-d'))"
            aria-label="Tanggal transaksi"
        />

        {{-- Program Kerja --}}
        <x-atoms.input
            as="select"
            name="program_id"
            label="Program Kerja"
            x-model="programId"
            aria-label="Pilih program kerja"
        >
            <option value="">Pilih program kerja (opsional)</option>
            @foreach ($programs as $program)
                <option value="{{ $program->id }}">{{ $program->name }}</option>
            @endforeach
        </x-atoms.input>

        {{-- No. Dokumen --}}
        <x-atoms.input
            name="document_number"
            label="No. Dokumen"
            x-model="nextDocumentNumber"
            required
            placeholder="Masukkan nomor dokumen (contoh: BKMUDD001)"
            aria-label="Nomor dokumen"
        />

        {{-- Terima Dari --}}
        <x-atoms.input
            as="select"
            name="user_id"
            label="Terima Dari"
            x-model="userId"
            required
            aria-label="Pilih terima dari"
        >
            <option value="">Pilih penerima / terima dari</option>
            @foreach ($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </x-atoms.input>

        {{-- Rekening Kas --}}
        <x-atoms.input
            as="select"
            name="cash_account_code"
            label="Rekening Kas"
            x-model="cashAccountCode"
            required
            aria-label="Pilih rekening kas"
        >
            <option value="">Pilih rekening kas</option>
            @foreach ($cashAccounts as $account)
                <option value="{{ $account->id }}">{{ $account->account_name }}</option>
            @endforeach
        </x-atoms.input>

        {{-- Kode Transaksi --}}
        <x-atoms.input
            as="select"
            name="transaction_account_code"
            label="Kode Transaksi"
            x-model="transactionAccountCode"
            x-on:change="handleTransactionAccountChange()"
            required
            aria-label="Pilih kode transaksi"
            :aria-busy="'loadingSuggestion'"
        >
            <option value="">Pilih kode transaksi</option>
            <template x-for="option in transactionAccountOptions" :key="option.id">
                <option :value="option.id" x-text="`${option.name}`"></option>
            </template>
        </x-atoms.input>

        {{-- Nominal --}}
        <div>
            <x-atoms.input
                name="amount"
                type="number"
                label="Nominal (Rp)"
                placeholder="Contoh: 500000"
                x-model="amount"
                min="1"
                step="1"
                required
                aria-label="Jumlah nominal transaksi"
            />
            <div class="mt-2 flex items-center justify-between">
                <span x-show="amount" x-cloak class="text-xs font-semibold text-primary" aria-live="polite">
                    Format: Rp <span x-text="Number(amount).toLocaleString('id-ID')"></span>
                </span>
                <div class="flex gap-1.5 ml-auto">
                    <button type="button" @click="amount = (amount ? amount + '000' : '1000')" class="px-2.5 py-1 text-[10px] font-bold bg-surface-muted text-content-base rounded-lg border border-surface-border hover:bg-surface-border transition duration-200" aria-label="Tambah tiga nol">
                        +000
                    </button>
                    <button type="button" @click="amount = (amount ? amount + '000000' : '1000000')" class="px-2.5 py-1 text-[10px] font-bold bg-surface-muted text-content-base rounded-lg border border-surface-border hover:bg-surface-border transition duration-200" aria-label="Tambah enam nol">
                        +000.000
                    </button>
                </div>
            </div>
        </div>

        {{-- Referensi (Nomor Kuitansi, dll) --}}
        <x-atoms.input
            name="reference"
            label="Referensi"
            placeholder="Nomor kuitansi, dll. (opsional)"
            x-model="reference"
            maxlength="100"
            aria-label="Nomor referensi transaksi"
        />

        {{-- Keterangan --}}
        <div class="md:col-span-2">
            <div class="mb-2 flex items-center justify-between">
                <label for="description" class="block text-xs font-bold uppercase tracking-normal text-content-base">
                    Keterangan
                </label>
                <span
                    x-show="loadingSuggestion"
                    x-cloak
                    class="inline-flex items-center gap-1.5 text-xs text-content-muted"
                    aria-live="polite"
                >
                    <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Memuat sugesti...
                </span>
            </div>
            <textarea
                id="description"
                name="description"
                x-model="description"
                rows="3"
                maxlength="500"
                placeholder="Keterangan transaksi (opsional, dapat diisi otomatis)"
                class="block w-full rounded-xl border border-surface-border bg-surface-base px-4 py-2.5 text-sm text-content-base transition duration-200 placeholder:text-content-subtle hover:border-content-subtle focus-visible:border-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-ring"
                aria-label="Keterangan transaksi"
                aria-describedby="description_hint"
            ></textarea>
            <p id="description_hint" class="mt-1.5 text-xs text-content-muted">
                Keterangan akan terisi otomatis jika kode transaksi yang dipilih memiliki sugesti.
            </p>
        </div>

        <x-molecules.form-actions
            submitText="Simpan Penerimaan Kas"
            submitXText="editingId ? 'Simpan Perubahan' : 'Simpan Penerimaan Kas'"
            cancelText="Batal Edit"
            cancelShow="editingId"
            cancelClick="cancelEdit()"
            loadingText="Menyimpan..."
            loadingShow="loadingSubmit"
            submitDisabled="loadingSubmit"
            submitBusy="loadingSubmit"
            submitAriaLabel="Simpan data penerimaan kas"
            cancelAriaLabel="Batal edit penerimaan kas"
        />
    </form>
</x-atoms.surface>
