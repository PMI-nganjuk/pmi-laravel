@props([
    'accountCategoryOptions' => [],
    'normalBalanceOptions' => [],
    'financialReportTypeOptions' => [],
])

<x-atoms.surface
    tag="section"
    x-ref="coaPanel"
    x-cloak
    x-transition
    aria-labelledby="coa-form-title"
>
    <x-molecules.form-header 
        id="coa-form-title"
        badgeXText="editingId ? 'Mode edit' : 'Input baru'"
        titleXText="editingId ? 'Edit Chart of Account' : 'Tambah Chart of Account'"
        subtitleXText="editingId ? 'Perbarui detail akun tanpa mengubah struktur kategori yang tidak diperlukan.' : 'Lengkapi kategori, nama akun, dan posisi laporan untuk menambahkan COA baru.'"
    />

    <form
        x-ref="coaForm"
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

        <x-atoms.input
            as="select"
            name="account_category_id"
            label="Kategori 1"
            x-model="accountCategoryId"
            x-on:change="handleAccountCategoryChange()"
            required
            aria-label="Select primary category"
        >
            <option value="">Pilih kategori utama</option>
            @foreach ($accountCategoryOptions as $id => $name)
                <option value="{{ $id }}">{{ $id }} - {{ $name }}</option>
            @endforeach
        </x-atoms.input>

        <div>
            <x-atoms.input
                as="select"
                name="account_subcategory_id"
                label="Kategori 2"
                x-model="accountSubcategoryId"
                x-on:change="handleAccountSubcategoryChange()"
                x-bind:disabled="!accountCategoryId || loadingAccountSubcategory"
                required
                aria-label="Select secondary category"
                aria-busy="loadingAccountSubcategory"
            >
                <option value="" x-text="accountSubcategoryPlaceholder"></option>
                <template x-for="option in accountSubcategoryOptions" :key="option.id">
                    <option :value="option.id" x-text="`${option.id} - ${option.name}`"></option>
                </template>
            </x-atoms.input>

            <p
                x-show="accountCategoryId && !loadingAccountSubcategory && accountSubcategoryOptions.length === 0"
                x-cloak
                class="mt-1.5 text-xs font-medium text-danger-text"
                role="alert"
                aria-live="polite"
            >
                Kategori 2 belum tersedia untuk kategori utama ini.
            </p>
        </div>

        <div class="md:col-span-2">
            <input type="hidden" name="id" x-model="accountId">

            <label class="mb-2 block text-xs font-bold uppercase tracking-normal text-content-base">
                Kode Akun <span class="text-danger" aria-hidden="true">*</span>
            </label>

            <div
                class="flex min-h-12 items-center rounded-xl border border-surface-border bg-surface-muted px-4 py-3 text-sm font-semibold text-content-base"
                aria-live="polite"
                aria-atomic="true"
            >
                <span x-show="!loadingCode" x-text="accountId || 'Pilih kategori 1 dan kategori 2 untuk membuat kode otomatis'"></span>
                <span x-show="loadingCode" x-cloak class="inline-flex items-center gap-2 text-content-muted">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Membuat kode akun...
                </span>
            </div>

            <p class="mt-1.5 text-xs text-content-muted">Kode akun dibuat otomatis dari kombinasi kategori agar konsisten dengan struktur COA.</p>
        </div>

        <div class="md:col-span-2">
            <x-atoms.input
                name="account_name"
                label="Nama Akun"
                placeholder="Contoh: Kas Operasional"
                maxlength="100"
                x-model="accountName"
                required
                aria-label="Account name input"
            />
        </div>

        <x-atoms.input as="select" name="normal_balance" label="Pos Saldo" x-model="normalBalance" required aria-label="Select balance position">
            <option value="">Pilih posisi saldo</option>
            @foreach ($normalBalanceOptions as $type)
                <option value="{{ $type->value }}">{{ $type->label() }}</option>
            @endforeach
        </x-atoms.input>

        <x-atoms.input as="select" name="financial_report_type_id" label="Pos Laporan" x-model="financialReportTypeId" required aria-label="Select report position">
            <option value="">Pilih laporan terkait</option>
            @foreach ($financialReportTypeOptions as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </x-atoms.input>

        <x-molecules.form-actions
            cancelText="Batal Edit"
            cancelShow="editingId"
            cancelClick="cancelEdit()"
            loadingShow="loadingSubmit"
            submitDisabled="loadingSubmit || loadingAccountSubcategory || loadingCode"
            submitBusy="loadingSubmit"
            submitXText="editingId ? 'Simpan Perubahan' : 'Simpan COA'"
            cancelAriaLabel="Cancel editing chart of account"
        />
    </form>
</x-atoms.surface>
