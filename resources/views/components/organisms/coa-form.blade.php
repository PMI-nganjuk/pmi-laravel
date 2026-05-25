@props([
    'categoryOneOptions' => [],
    'entryTypeOptions' => [],
    'reportTypeOptions' => [],
])

<x-atoms.surface
    tag="section"
    x-ref="coaPanel"
    x-cloak
    x-transition
    aria-labelledby="coa-form-title"
>
    <div class="mb-6 flex flex-col justify-between gap-4 border-b border-surface-border pb-5 sm:flex-row sm:items-start">
        <div>
            <p class="text-xs font-bold uppercase tracking-normal text-primary" x-text="editingId ? 'Mode edit' : 'Input baru'"></p>
            <h2 id="coa-form-title" class="mt-1 text-lg font-bold text-content-base" x-text="editingId ? 'Edit Chart of Account' : 'Tambah Chart of Account'"></h2>
            <p class="mt-1 text-sm text-content-muted" x-text="editingId ? 'Perbarui detail akun tanpa mengubah struktur kategori yang tidak diperlukan.' : 'Lengkapi kategori, nama akun, dan posisi laporan untuk menambahkan COA baru.'"></p>
        </div>
    </div>

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
            name="category_one"
            label="Kategori 1"
            x-model="categoryOne"
            x-on:change="handleCategoryOneChange()"
            required
            aria-label="Select primary category"
        >
            <option value="">Pilih kategori utama</option>
            @foreach ($categoryOneOptions as $code => $name)
                <option value="{{ $code }}">{{ $code }} - {{ $name }}</option>
            @endforeach
        </x-atoms.input>

        <div>
            <x-atoms.input
                as="select"
                name="category_two"
                label="Kategori 2"
                x-model="categoryTwo"
                x-on:change="handleCategoryTwoChange()"
                x-bind:disabled="!categoryOne || loadingCategoryTwo"
                required
                aria-label="Select secondary category"
                aria-busy="loadingCategoryTwo"
            >
                <option value="" x-text="categoryTwoPlaceholder"></option>
                <template x-for="option in categoryTwoOptions" :key="option.code">
                    <option :value="option.code" x-text="`${option.code} - ${option.name}`"></option>
                </template>
            </x-atoms.input>

            <p
                x-show="categoryOne && !loadingCategoryTwo && categoryTwoOptions.length === 0"
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

        <x-atoms.input as="select" name="entry_type" label="Pos Saldo" x-model="entryType" required aria-label="Select balance position">
            <option value="">Pilih posisi saldo</option>
            @foreach ($entryTypeOptions as $type)
                <option value="{{ $type->value }}">{{ $type->label() }}</option>
            @endforeach
        </x-atoms.input>

        <x-atoms.input as="select" name="report_type_id" label="Pos Laporan" x-model="reportTypeId" required aria-label="Select report position">
            <option value="">Pilih laporan terkait</option>
            @foreach ($reportTypeOptions as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </x-atoms.input>

        <div class="mt-2 flex flex-col-reverse gap-3 border-t border-surface-border pt-5 sm:flex-row sm:justify-end md:col-span-2">
            <x-atoms.button
                type="button"
                variant="secondary"
                size="md"
                x-show="editingId"
                x-on:click="cancelEdit()"
                aria-label="Cancel editing chart of account"
            >
                Batal Edit
            </x-atoms.button>

            <x-atoms.button
                type="submit"
                variant="primary"
                size="md"
                x-bind:disabled="loadingSubmit || loadingCategoryTwo || loadingCode"
                x-bind:aria-busy="loadingSubmit"
                aria-label="editingId ? 'Save changes to chart of account' : 'Save new chart of account'"
            >
                <span x-show="loadingSubmit" x-cloak>
                    Menyimpan...
                </span>
                <span x-show="!loadingSubmit" x-text="editingId ? 'Simpan Perubahan' : 'Simpan COA'"></span>
            </x-atoms.button>
        </div>
    </form>
</x-atoms.surface>
