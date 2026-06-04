@props([
    'allAccounts' => collect(),
    'programs'    => collect(),
    'users'       => collect(),
])

<x-atoms.surface
    tag="section"
    aria-labelledby="adjusting-entry-form-title"
    x-cloak
    x-transition
>
    <x-molecules.form-header
        id="adjusting-entry-form-title"
        badgeXText="editingId ? 'Mode edit' : 'Input Baru'"
        titleXText="editingId ? 'Edit Jurnal Penyesuaian' : 'Tambah Jurnal Penyesuaian'"
        subtitleXText="editingId ? 'Perbarui detail transaksi jurnal penyesuaian.' : 'Isi detail transaksi jurnal penyesuaian manual (double-entry).'"
    />

    <form
        x-ref="adjustingEntryForm"
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

        {{-- Tanggal --}}
        <x-atoms.input
            name="transaction_date"
            type="date"
            label="Tanggal"
            x-model="transactionDate"
            required
            :value="old('transaction_date', date('Y-m-d'))"
            aria-label="Tanggal penyesuaian"
        />

        {{-- No. Dokumen --}}
        <x-atoms.input
            name="document_number"
            label="No. Dokumen"
            x-model="nextDocumentNumber"
            required
            placeholder="Masukkan nomor dokumen (contoh: BKJUDD001)"
            aria-label="Nomor dokumen penyesuaian"
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

        {{-- Referensi --}}
        <x-atoms.input
            name="reference"
            label="Referensi"
            placeholder="Masukkan referensi (opsional)"
            x-model="reference"
            maxlength="100"
            aria-label="Nomor referensi transaksi penyesuaian"
        />

        {{-- COA Transaksi --}}
        <x-atoms.input
            as="select"
            name="debit_account_id"
            label="COA Transaksi (Debit)"
            x-model="debitAccountId"
            required
            aria-label="Pilih akun debit"
        >
            <option value="">Pilih COA Transaksi</option>
            @foreach ($allAccounts as $account)
                <option value="{{ $account->id }}">{{ $account->account_name }}</option>
            @endforeach
        </x-atoms.input>

        {{-- Lawan COA Transaksi --}}
        <x-atoms.input
            as="select"
            name="credit_account_id"
            label="Lawan COA Transaksi (Kredit)"
            x-model="creditAccountId"
            required
            aria-label="Pilih akun kredit"
        >
            <option value="">Pilih Lawan COA Transaksi</option>
            @foreach ($allAccounts as $account)
                <option value="{{ $account->id }}">{{ $account->account_name }}</option>
            @endforeach
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
                aria-label="Jumlah nominal penyesuaian"
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

        {{-- Entri Jurnal --}}
        <x-atoms.input
            as="select"
            name="journal_entry_type"
            label="Entri Jurnal"
            x-model="journalEntryType"
            required
            aria-label="Pilih entri jurnal"
        >
            <option value="">Pilih Entri Jurnal</option>
            <option value="BEGINNING_BALANCES">Saldo Awal</option>
            <option value="ADJUSTING_ENTRIES">Lainnya</option>
        </x-atoms.input>

        {{-- Keterangan --}}
        <div class="md:col-span-2">
            <label for="adjusting-entry-description" class="block text-xs font-bold uppercase tracking-normal text-content-base mb-2">
                Keterangan
            </label>
            <textarea
                id="adjusting-entry-description"
                name="description"
                x-model="description"
                rows="3"
                maxlength="500"
                placeholder="Keterangan transaksi (opsional)"
                class="block w-full rounded-xl border border-surface-border bg-surface-base px-4 py-2.5 text-sm text-content-base transition duration-200 placeholder:text-content-subtle hover:border-content-subtle focus-visible:border-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-ring"
                aria-label="Keterangan transaksi penyesuaian"
            ></textarea>
        </div>

        {{-- Hidden User ID --}}
        <input type="hidden" name="user_id" x-model="userId">

        <x-molecules.form-actions
            submitText="Simpan Jurnal Penyesuaian"
            submitXText="editingId ? 'Simpan Perubahan' : 'Simpan Jurnal Penyesuaian'"
            cancelText="Batal Edit"
            cancelShow="editingId"
            cancelClick="cancelEdit()"
            loadingText="Menyimpan..."
            loadingShow="loadingSubmit"
            submitDisabled="loadingSubmit"
            submitBusy="loadingSubmit"
            submitAriaLabel="Simpan data jurnal penyesuaian"
            cancelAriaLabel="Batal edit jurnal penyesuaian"
        />
    </form>
</x-atoms.surface>
