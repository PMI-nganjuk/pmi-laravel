<x-app-layout>
    <x-layout.shell
        page-title="Program Kerja"
        x-data="programPage"
        x-on:click="handlePageClick($event)"
    >
        <main class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Session Alerts -->
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

            <!-- Form Validation Alerts -->
            @if ($errors->any())
                <x-atoms.alert variant="danger">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-atoms.alert>
            @endif


            <!-- Program Form (Inline) -->
            <x-atoms.surface
                tag="section"
                x-ref="programPanel"
                aria-labelledby="program-form-title"
            >
                <div class="mb-6 flex flex-col justify-between gap-4 border-b border-surface-border pb-5 sm:flex-row sm:items-start">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-normal text-primary" x-text="editingId ? 'Mode edit' : 'Input baru'"></p>
                        <h2 id="program-form-title" class="mt-1 text-lg font-bold text-content-base" x-text="editingId ? 'Edit Program Kerja' : 'Tambah Program Kerja'">Tambah Program Kerja</h2>
                        <p class="mt-1 text-sm text-content-muted" x-text="editingId ? 'Perbarui informasi program kerja dan PIC pelaksana.' : 'Daftarkan program kerja baru dengan menunjuk PIC pelaksana yang tepat.'"></p>
                    </div>

                    <x-atoms.button
                        type="button"
                        variant="secondary"
                        size="sm"
                        x-show="editingId"
                        x-on:click="cancelEdit()"
                        aria-label="Batal edit program kerja"
                        style="display: none;"
                    >
                        Batal Edit
                    </x-atoms.button>
                </div>

                <form
                    x-ref="programForm"
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

                    <!-- Name field -->
                    <x-atoms.input
                        name="name"
                        label="Nama Program Kerja"
                        required
                        x-model="name"
                        placeholder="Masukkan nama program kerja"
                    />

                    <!-- PIC / User selection -->
                    <x-atoms.input as="select" name="user_id" label="Penanggung Jawab (PIC)" x-model="userId" required>
                        <option value="" disabled>Pilih PIC program</option>
                        @foreach ($userOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </x-atoms.input>

                    <!-- Description field -->
                    <div class="md:col-span-2">
                        <x-atoms.input
                            name="description"
                            label="Keterangan Program"
                            x-model="description"
                            placeholder="Penjelasan ringkas mengenai sasaran or detail program kerja"
                        />
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-2 flex flex-col-reverse gap-3 border-t border-surface-border pt-5 sm:flex-row sm:justify-end md:col-span-2">
                        <x-atoms.button
                            type="submit"
                            variant="primary"
                            size="md"
                            x-bind:disabled="loadingSubmit"
                            x-bind:aria-busy="loadingSubmit"
                        >
                            <span x-show="loadingSubmit" x-cloak>
                                Menyimpan...
                            </span>
                            <span x-show="!loadingSubmit" x-text="editingId ? 'Simpan Perubahan' : 'Registrasikan Program'">Registrasikan Program</span>
                        </x-atoms.button>
                    </div>
                </form>
            </x-atoms.surface>

            <!-- Programs table card with datatable wrapper -->
            <x-organisms.datatable-wrapper
                :endpoint="route('programs.index')"
                default-sort="name"
                default-dir="asc"
                :filters="['user_id' => '']"
            >
                <x-molecules.datatable-filters placeholder="Cari nama atau keterangan...">
                    <div class="w-full md:w-64 relative">
                        <select
                            x-model="filters.user_id"
                            x-on:change="fetchData()"
                            class="appearance-none block w-full pl-4 pr-10 py-2.5 bg-surface-base border border-surface-border hover:border-content-subtle focus-visible:border-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-ring/25 text-content-base text-sm rounded-xl focus:outline-none transition duration-200"
                            aria-label="Filter PIC"
                        >
                            <option value="">Semua PIC</option>
                            @foreach ($userOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-content-muted" aria-hidden="true">
                            <x-atoms.icon name="chevron-down" class="h-4 w-4" />
                        </div>
                    </div>
                </x-molecules.datatable-filters>

                <!-- Data container -->
                <div id="data-container">
                    <x-organisms.table
                        :is-empty="$programs->isEmpty()"
                        empty-title="Tidak Ada Program Kerja"
                        empty-message="Coba sesuaikan kata kunci pencarian atau filter PIC Anda untuk menemukan program kerja yang dicari."
                    >
                        <x-slot:headers>
                            <x-atoms.table-sort-head column="id" label="No" />
                            <x-atoms.table-sort-head column="name" label="Nama Program Kerja" />
                            <x-atoms.table-head>Penanggung Jawab (PIC)</x-atoms.table-head>
                            <x-atoms.table-head>Keterangan</x-atoms.table-head>
                            <x-atoms.table-sort-head column="created_at" label="Tanggal Dibuat" />
                            <x-atoms.table-head>Aksi</x-atoms.table-head>
                        </x-slot:headers>

                        @foreach ($programs as $p)
                            <x-atoms.table-row>
                                <x-atoms.table-cell class="whitespace-nowrap font-bold text-content-base">
                                    {{ $p->id }}
                                </x-atoms.table-cell>

                                <x-atoms.table-cell class="font-semibold text-content-base">
                                    {{ $p->name }}
                                </x-atoms.table-cell>

                                <x-atoms.table-cell class="font-medium">
                                    {{ $p->user?->name ?? '-' }}
                                </x-atoms.table-cell>

                                <x-atoms.table-cell class="max-w-xs truncate font-medium text-content-muted">
                                    {{ $p->description ?? '-' }}
                                </x-atoms.table-cell>

                                <x-atoms.table-cell class="font-medium">
                                    {{ $p->created_at ? $p->created_at->format('d M Y') : '-' }}
                                </x-atoms.table-cell>

                                <x-atoms.table-cell>
                                    <div class="flex items-center gap-2">
                                        <x-atoms.button
                                            type="button"
                                            variant="secondary"
                                            size="sm"
                                            data-program-edit
                                            data-program-id="{{ $p->id }}"
                                            data-program-name="{{ $p->name }}"
                                            data-program-user-id="{{ $p->user_id }}"
                                            data-program-description="{{ $p->description }}"
                                        >
                                            <x-atoms.icon name="edit" class="h-3.5 w-3.5 mr-1" />
                                            Edit
                                        </x-atoms.button>

                                        <form action="{{ route('programs.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus program kerja {{ $p->name }}? Tindakan ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <x-atoms.button type="submit" variant="danger" size="sm">
                                                <x-atoms.icon name="trash" class="h-3.5 w-3.5 mr-1" />
                                                Hapus
                                            </x-atoms.button>
                                        </form>
                                    </div>
                                </x-atoms.table-cell>
                            </x-atoms.table-row>
                        @endforeach
                    </x-organisms.table>

                    @if ($programs->hasPages())
                        <div class="border-t border-surface-border bg-surface-base px-6 py-4">
                            {{ $programs->links() }}
                        </div>
                    @endif
                </div>
            </x-organisms.datatable-wrapper>
        </main>
    </x-layout.shell>

    <script>
        function initProgramPage() {
            Alpine.data('programPage', () => ({
                sidebarOpen: false,
                editingId: @js(old('_editing_id', '')),
                name: @js(old('name', '')),
                userId: @js(old('user_id', '')),
                description: @js(old('description', '')),
                storeUrl: @js(route('programs.store')),
                updateBaseUrl: @js(url('programs')),
                loadingSubmit: false,

                get formAction() {
                    return this.editingId
                        ? `${this.updateBaseUrl}/${encodeURIComponent(this.editingId)}`
                        : this.storeUrl;
                },

                handlePageClick(event) {
                    const editButton = event.target.closest('[data-program-edit]');
                    if (!editButton) return;
                    event.preventDefault();
                    this.editProgram({
                        id: editButton.dataset.programId,
                        name: editButton.dataset.programName,
                        user_id: editButton.dataset.programUserId,
                        description: editButton.dataset.programDescription,
                    });
                },

                editProgram(program) {
                    this.editingId = String(program.id || '');
                    this.name = String(program.name || '');
                    this.userId = String(program.user_id || '');
                    this.description = String(program.description || '');
                    this.scrollToForm();
                },

                cancelEdit() {
                    this.editingId = '';
                    this.name = '';
                    this.userId = '';
                    this.description = '';
                },

                submitForm() {
                    this.loadingSubmit = true;
                    this.$refs.programForm.submit();
                },

                scrollToForm() {
                    this.$nextTick(() => {
                        this.$refs.programPanel?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                }
            }));
        }

        if (window.Alpine) {
            initProgramPage();
        } else {
            document.addEventListener('alpine:init', initProgramPage);
        }
    </script>
</x-app-layout>
