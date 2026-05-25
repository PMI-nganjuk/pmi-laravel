<x-app-layout>
    <div
        class="coa-page"
        x-data="createCoaPageComponent({
            storeUrl: @js(route('coa.store')),
            updateBaseUrl: @js(url('/coa')),
            categoryTwoUrl: @js(route('coa.category-two')),
            generateCodeUrl: @js(route('coa.generate-code')),
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
</x-app-layout>
