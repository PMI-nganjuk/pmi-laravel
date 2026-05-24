<x-app-layout>
    <div
        class="coa-page"
        x-data="coaPage({
            storeUrl: @js(route('coa.store')),
            updateBaseUrl: @js(url('/coa')),
            categoryTwoUrl: @js(route('coa.category-two')),
            generateCodeUrl: @js(route('coa.generate-code')),
            initialShowForm: @js($errors->any() || old('account_name') !== null),
            initialEditingId: @js(old('_editing_id', '')),
            initialCategoryOne: @js(old('category_one', '')),
            initialCategoryTwo: @js(old('category_two', '')),
            initialAccountId: @js(old('id', '')),
            initialAccountName: @js(old('account_name', '')),
            initialEntryType: @js(old('entry_type', '')),
            initialReportTypeId: @js(old('report_type_id', '')),
        })"
        x-init="init()"
        x-on:click="handlePageClick($event)"
        x-on:submit="handlePageSubmit($event)"
    >
        <div class="fixed inset-0 z-40 bg-content-base/60 backdrop-blur-sm lg:hidden"
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
            <x-layout.header />

            <main class="coa-main">
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
                    <x-atoms.alert variant="danger" title="Form COA belum bisa disimpan.">
                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-atoms.alert>
                @endif

                <section class="coa-panel flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between" aria-labelledby="coa-page-title">
                    <div>
                        <h2 id="coa-page-title" class="text-xl font-bold text-content-base">Daftar Chart of Accounts</h2>
                        <p class="mt-1 max-w-2xl text-sm text-content-muted">
                            Kelola kode akun, kategori, posisi saldo, dan relasi laporan untuk pencatatan keuangan PMI.
                        </p>
                    </div>

                    <x-atoms.button
                        type="button"
                        variant="primary"
                        size="md"
                        x-on:click="toggleCreateForm()"
                        aria-controls="coa-form-title"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span x-text="showForm && !editingId ? 'Tutup Form' : 'Tambah COA'"></span>
                    </x-atoms.button>
                </section>

                <x-organisms.coa-form
                    :category-one-options="$categoryOneOptions"
                    :entry-type-options="$entryTypeOptions"
                    :report-type-options="$reportTypeOptions"
                />

                <x-organisms.datatable-wrapper
                    :endpoint="route('coa.index')"
                    default-sort="id"
                    default-dir="asc"
                    :filters="['entry_type' => '', 'report_type_id' => '']"
                >
                    <x-molecules.datatable-filters placeholder="Cari kode atau nama akun...">
                        <select
                            x-model="filters.entry_type"
                            x-on:change="fetchData()"
                            class="coa-filter-select w-full md:w-64"
                            aria-label="Filter pos saldo"
                        >
                            <option value="">Semua Pos Saldo</option>
                            @foreach ($entryTypeOptions as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                            @endforeach
                        </select>

                        <select
                            x-model="filters.report_type_id"
                            x-on:change="fetchData()"
                            class="coa-filter-select w-full md:w-64"
                            aria-label="Filter pos laporan"
                        >
                            <option value="">Semua Pos Laporan</option>
                            @foreach ($reportTypeOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </x-molecules.datatable-filters>

                    <div id="data-container">
                        <x-organisms.table
                            :is-empty="$coas->isEmpty()"
                            empty-title="Belum Ada COA"
                            empty-message="Tambahkan COA pertama atau sesuaikan pencarian dan filter yang sedang aktif."
                            class="border-0 shadow-none"
                        >
                            <x-slot:headers>
                                <x-atoms.table-sort-head column="id" label="Kode Akun" />
                                <x-atoms.table-sort-head column="account_name" label="Nama Akun" />
                                <x-atoms.table-head>Kategori 1</x-atoms.table-head>
                                <x-atoms.table-head>Kategori 2</x-atoms.table-head>
                                <x-atoms.table-sort-head column="entry_type" label="Pos Saldo" />
                                <x-atoms.table-sort-head column="report_type_id" label="Pos Laporan" />
                                <x-atoms.table-head class="text-right">Aksi</x-atoms.table-head>
                            </x-slot:headers>

                            @foreach ($coas as $coa)
                                <x-atoms.table-row>
                                    <x-atoms.table-cell class="whitespace-nowrap font-bold text-content-base">
                                        {{ $coa->id }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell>
                                        <div class="font-semibold text-content-base">{{ $coa->account_name }}</div>
                                        <div class="mt-0.5 text-xs text-content-subtle">COA {{ $coa->id }}</div>
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell>
                                        {{ $coa->categoryTwo?->categoryOne?->category_name ?? '-' }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell>
                                        {{ $coa->categoryTwo?->category_name ?? '-' }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell>
                                        <x-atoms.badge :variant="$coa->entry_type === \App\Enums\EntryTypeEnum::DEBIT->value ? 'success' : 'warning'">
                                            {{ $coa->entry_type === \App\Enums\EntryTypeEnum::DEBIT->value ? 'Debit' : 'Kredit' }}
                                        </x-atoms.badge>
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell>
                                        {{ $coa->reportType?->report_name ?? '-' }}
                                    </x-atoms.table-cell>

                                    <x-atoms.table-cell class="text-right">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <x-atoms.button
                                                type="button"
                                                variant="secondary"
                                                size="sm"
                                                data-coa-edit
                                                data-coa-id="{{ $coa->id }}"
                                                data-coa-category-one="{{ $coa->categoryTwo?->category_one ?? $coa->category_one }}"
                                                data-coa-category-two="{{ $coa->category_two }}"
                                                data-coa-account-name="{{ $coa->account_name }}"
                                                data-coa-entry-type="{{ $coa->entry_type }}"
                                                data-coa-report-type-id="{{ $coa->report_type_id }}"
                                                :aria-label="'Edit COA ' . $coa->id"
                                            >
                                                Edit
                                            </x-atoms.button>

                                            <form
                                                action="{{ route('coa.destroy', $coa->id) }}"
                                                method="POST"
                                                data-confirm-submit="Hapus COA {{ $coa->id }}?"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <x-atoms.button type="submit" variant="danger" size="sm" :aria-label="'Delete COA ' . $coa->id">
                                                    Hapus
                                                </x-atoms.button>
                                            </form>
                                        </div>
                                    </x-atoms.table-cell>
                                </x-atoms.table-row>
                            @endforeach
                        </x-organisms.table>

                        @if ($coas->hasPages())
                            <div class="border-t border-surface-border bg-surface-base px-6 py-4">
                                {{ $coas->links() }}
                            </div>
                        @endif
                    </div>
                </x-organisms.datatable-wrapper>
            </main>
        </div>
    </div>

    <script>
        function coaPage(config) {
            return {
                sidebarOpen: false,
                showForm: Boolean(config.initialShowForm),
                storeUrl: config.storeUrl,
                updateBaseUrl: config.updateBaseUrl,
                categoryTwoUrl: config.categoryTwoUrl,
                generateCodeUrl: config.generateCodeUrl,
                editingId: config.initialEditingId || '',
                categoryOne: config.initialCategoryOne || '',
                categoryTwo: config.initialCategoryTwo || '',
                categoryTwoOptions: [],
                accountId: config.initialAccountId || '',
                accountName: config.initialAccountName || '',
                entryType: config.initialEntryType || '',
                reportTypeId: config.initialReportTypeId || '',
                loadingCategoryTwo: false,
                loadingCode: false,
                loadingSubmit: false,

                get formAction() {
                    return this.editingId
                        ? `${this.updateBaseUrl}/${encodeURIComponent(this.editingId)}`
                        : this.storeUrl;
                },

                get categoryTwoPlaceholder() {
                    if (!this.categoryOne) {
                        return 'Pilih kategori utama dulu';
                    }

                    return this.loadingCategoryTwo ? 'Memuat kategori 2...' : 'Pilih kategori 2';
                },

                async init() {
                    if (this.categoryOne) {
                        await this.loadCategoryTwoOptions();

                        if (this.categoryTwo && !this.accountId) {
                            await this.updateCode();
                        }
                    }
                },

                handlePageClick(event) {
                    const editButton = event.target.closest('[data-coa-edit]');

                    if (!editButton) {
                        return;
                    }

                    event.preventDefault();

                    this.editCoa({
                        id: editButton.dataset.coaId,
                        category_one: editButton.dataset.coaCategoryOne,
                        category_two: editButton.dataset.coaCategoryTwo,
                        account_name: editButton.dataset.coaAccountName,
                        entry_type: editButton.dataset.coaEntryType,
                        report_type_id: editButton.dataset.coaReportTypeId,
                    });
                },

                handlePageSubmit(event) {
                    const form = event.target.closest('[data-confirm-submit]');

                    if (!form) {
                        return;
                    }

                    if (!confirm(form.dataset.confirmSubmit)) {
                        event.preventDefault();
                    }
                },

                toggleCreateForm() {
                    if (this.showForm && !this.editingId) {
                        this.closeForm();
                        return;
                    }

                    this.cancelEdit(false);
                    this.showForm = true;
                    this.scrollToForm();
                },

                closeForm() {
                    this.cancelEdit(false);
                    this.showForm = false;
                },

                cancelEdit(keepOpen = true) {
                    this.editingId = '';
                    this.categoryOne = '';
                    this.categoryTwo = '';
                    this.categoryTwoOptions = [];
                    this.accountId = '';
                    this.accountName = '';
                    this.entryType = '';
                    this.reportTypeId = '';
                    this.loadingSubmit = false;
                    this.showForm = keepOpen ? true : this.showForm;
                },

                async editCoa(record) {
                    this.showForm = true;
                    this.editingId = String(record.id || '');
                    this.categoryOne = String(record.category_one || '');
                    this.categoryTwo = '';
                    this.accountId = String(record.id || '');
                    this.accountName = String(record.account_name || '');
                    this.entryType = String(record.entry_type || '');
                    this.reportTypeId = String(record.report_type_id || '');

                    await this.loadCategoryTwoOptions();
                    this.categoryTwo = String(record.category_two || '');
                    this.scrollToForm();
                },

                async handleCategoryOneChange() {
                    this.categoryTwo = '';
                    this.accountId = '';
                    await this.loadCategoryTwoOptions();
                },

                async handleCategoryTwoChange() {
                    await this.updateCode();
                },

                async loadCategoryTwoOptions() {
                    this.categoryTwoOptions = [];

                    if (!this.categoryOne) {
                        return;
                    }

                    this.loadingCategoryTwo = true;

                    try {
                        const params = new URLSearchParams({ category_one: this.categoryOne });
                        const response = await fetch(`${this.categoryTwoUrl}?${params.toString()}`, {
                            headers: { Accept: 'application/json' },
                        });

                        if (!response.ok) {
                            throw new Error('Gagal memuat kategori 2.');
                        }

                        const payload = await response.json();
                        this.categoryTwoOptions = Object.entries(payload.data || {}).map(([code, name]) => ({ code, name }));
                    } catch (error) {
                        console.error(error);
                        this.categoryTwoOptions = [];
                    } finally {
                        this.loadingCategoryTwo = false;
                    }
                },

                async updateCode() {
                    this.accountId = '';

                    if (!this.categoryOne || !this.categoryTwo) {
                        return;
                    }

                    this.loadingCode = true;

                    try {
                        const params = new URLSearchParams({
                            category_one: this.categoryOne,
                            category_two: this.categoryTwo,
                        });
                        const response = await fetch(`${this.generateCodeUrl}?${params.toString()}`, {
                            headers: { Accept: 'application/json' },
                        });

                        if (!response.ok) {
                            throw new Error('Gagal membuat kode akun.');
                        }

                        const payload = await response.json();
                        this.accountId = payload.data?.code || '';
                    } catch (error) {
                        console.error(error);
                        this.accountId = '';
                    } finally {
                        this.loadingCode = false;
                    }
                },

                submitForm() {
                    if (!this.accountId) {
                        alert('Silakan pilih kategori 1 dan kategori 2 terlebih dahulu.');
                        return;
                    }

                    this.loadingSubmit = true;
                    this.$refs.coaForm.submit();
                },

                scrollToForm() {
                    this.$nextTick(() => {
                        this.$refs.coaPanel?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                },
            };
        }
    </script>
</x-app-layout>
