<x-app-layout>
    <x-layout.shell
        page-title="Manajemen Akun"
        x-data="userPage"
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


            <!-- User Form (Inline) -->
            <x-atoms.surface
                tag="section"
                x-ref="userPanel"
                aria-labelledby="user-form-title"
            >
                <div class="mb-6 flex flex-col justify-between gap-4 border-b border-surface-border pb-5 sm:flex-row sm:items-start">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-normal text-primary" x-text="editingId ? 'Mode edit' : 'Input baru'"></p>
                        <h2 id="user-form-title" class="mt-1 text-lg font-bold text-content-base" x-text="editingId ? 'Edit Akun Pengguna' : 'Tambah Akun Pengguna'"></h2>
                        <p class="mt-1 text-sm text-content-muted" x-text="editingId ? 'Perbarui informasi akun pengguna dan peran sistemnya.' : 'Daftarkan akun pengguna baru dengan menetapkan peran (role) yang tepat.'"></p>
                    </div>

                    <x-atoms.button
                        type="button"
                        variant="secondary"
                        size="sm"
                        x-show="editingId"
                        x-on:click="cancelEdit()"
                        aria-label="Batal edit pengguna"
                        style="display: none;"
                    >
                        Batal Edit
                    </x-atoms.button>
                </div>

                <form
                    x-ref="userForm"
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
                        label="Nama Lengkap"
                        required
                        x-model="name"
                        placeholder="Masukkan nama lengkap"
                    />

                    <!-- Email field -->
                    <x-atoms.input
                        name="email"
                        type="email"
                        label="Alamat Email"
                        required
                        x-model="email"
                        placeholder="nama@pmi-nganjuk.or.id"
                    />

                    <!-- Role field selection -->
                    <x-atoms.input as="select" name="role" label="Peran Sistem (Role)" x-model="role" required>
                        <option value="" disabled>Pilih peran pengguna</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r->value }}">
                                {{ $r->getLabel() }}
                            </option>
                        @endforeach
                    </x-atoms.input>

                    <!-- Password field -->
                    <x-atoms.input
                        name="password"
                        type="password"
                        x-model="password"
                        x-bind:label="editingId ? 'Kata Sandi Baru (Kosongkan jika tidak ingin mengubah)' : 'Kata Sandi Sementara'"
                        x-bind:required="!editingId"
                        placeholder="Minimal 8 karakter"
                    />

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
                            <span x-show="!loadingSubmit" x-text="editingId ? 'Simpan Perubahan' : 'Registrasikan Akun'">Registrasikan Akun</span>
                        </x-atoms.button>
                    </div>
                </form>
            </x-atoms.surface>

            <!-- Users table card with datatable wrapper -->
            <x-organisms.datatable-wrapper
                :endpoint="route('users.index')"
                default-sort="name"
                default-dir="asc"
                :filters="['role' => '']"
            >
                <x-molecules.datatable-filters placeholder="Cari nama atau email...">
                    <div class="w-full md:w-64 relative">
                        <select
                            x-model="filters.role"
                            x-on:change="fetchData()"
                            class="appearance-none block w-full pl-4 pr-10 py-2.5 bg-surface-base border border-surface-border hover:border-content-subtle focus-visible:border-primary text-content-base text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-ring/25 transition duration-200"
                            aria-label="Filter Peran"
                        >
                            <option value="">Semua Peran</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->value }}">
                                    {{ $r->getLabel() }}
                                </option>
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
                        :is-empty="$users->isEmpty()"
                        empty-title="Tidak Ada Pengguna Ditemukan"
                        empty-message="Coba sesuaikan kata kunci pencarian atau filter peran Anda untuk menemukan hasil yang dicari."
                    >
                        <x-slot:headers>
                            <x-atoms.table-sort-head column="name" label="Nama Pengguna" />
                            <x-atoms.table-sort-head column="email" label="Alamat Email" />
                            <x-atoms.table-head>Peran (Role)</x-atoms.table-head>
                            <x-atoms.table-sort-head column="created_at" label="Tanggal Bergabung" />
                            <x-atoms.table-head>Status</x-atoms.table-head>
                            <x-atoms.table-head>Aksi</x-atoms.table-head>
                        </x-slot:headers>

                        @foreach ($users as $u)
                            <x-atoms.table-row>
                                <x-atoms.table-cell class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-surface-muted flex items-center justify-center border border-surface-border text-content-base text-xs font-bold shadow-inner">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <span class="font-bold text-content-base">{{ $u->name }}</span>
                                </x-atoms.table-cell>

                                <x-atoms.table-cell class="font-medium">
                                    {{ $u->email }}
                                </x-atoms.table-cell>

                                <x-atoms.table-cell>
                                    <x-atoms.badge :variant="$u->role->getBadgeVariant()">
                                        {{ $u->role->getLabel() }}
                                    </x-atoms.badge>
                                </x-atoms.table-cell>

                                <x-atoms.table-cell class="font-medium">
                                    {{ $u->created_at ? $u->created_at->format('d M Y, H:i') : '-' }}
                                </x-atoms.table-cell>

                                <x-atoms.table-cell>
                                    <span class="inline-flex items-center gap-1.5 text-xs text-success-text font-semibold bg-success-bg px-2 py-0.5 border border-success-border rounded-full">
                                        <span class="h-1.5 w-1.5 rounded-full bg-success-text/80 animate-pulse"></span>
                                        Aktif
                                    </span>
                                </x-atoms.table-cell>

                                <x-atoms.table-cell>
                                    <div class="flex items-center gap-2">
                                        <x-atoms.button
                                            type="button"
                                            variant="secondary"
                                            size="sm"
                                            data-user-edit
                                            data-user-id="{{ $u->id }}"
                                            data-user-name="{{ $u->name }}"
                                            data-user-email="{{ $u->email }}"
                                            data-user-role="{{ $u->role->value }}"
                                        >
                                            <x-atoms.icon name="edit" class="h-3.5 w-3.5 mr-1" />
                                            Edit
                                        </x-atoms.button>

                                        @if (Auth::id() !== $u->id)
                                            <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->name }}? Tindakan ini tidak dapat dibatalkan.');">
                                                @csrf
                                                @method('DELETE')
                                                <x-atoms.button type="submit" variant="danger" size="sm">
                                                    <x-atoms.icon name="trash" class="h-3.5 w-3.5 mr-1" />
                                                    Hapus
                                                </x-atoms.button>
                                            </form>
                                        @else
                                            <span class="text-xs text-content-muted italic px-2">Akun Anda</span>
                                        @endif
                                    </div>
                                </x-atoms.table-cell>
                            </x-atoms.table-row>
                        @endforeach
                    </x-organisms.table>

                    @if ($users->hasPages())
                        <div class="border-t border-surface-border bg-surface-base px-6 py-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </x-organisms.datatable-wrapper>
        </main>
    </x-layout.shell>

    <script>
        function initUserPage() {
            Alpine.data('userPage', () => ({
                sidebarOpen: false,
                editingId: @js(old('_editing_id', '')),
                name: @js(old('name', '')),
                email: @js(old('email', '')),
                role: @js(old('role', '')),
                password: '',
                storeUrl: @js(route('users.store')),
                updateBaseUrl: @js(url('dashboard/users')),
                loadingSubmit: false,

                get formAction() {
                    return this.editingId
                        ? `${this.updateBaseUrl}/${encodeURIComponent(this.editingId)}`
                        : this.storeUrl;
                },

                handlePageClick(event) {
                    const editButton = event.target.closest('[data-user-edit]');
                    if (!editButton) return;
                    event.preventDefault();
                    this.editUser({
                        id: editButton.dataset.userId,
                        name: editButton.dataset.userName,
                        email: editButton.dataset.userEmail,
                        role: editButton.dataset.userRole,
                    });
                },

                editUser(user) {
                    this.editingId = String(user.id || '');
                    this.name = String(user.name || '');
                    this.email = String(user.email || '');
                    this.role = String(user.role || '');
                    this.password = '';
                    this.scrollToForm();
                },

                cancelEdit() {
                    this.editingId = '';
                    this.name = '';
                    this.email = '';
                    this.role = '';
                    this.password = '';
                },

                submitForm() {
                    this.loadingSubmit = true;
                    this.$refs.userForm.submit();
                },

                scrollToForm() {
                    this.$nextTick(() => {
                        this.$refs.userPanel?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                }
            }));
        }

        if (window.Alpine) {
            initUserPage();
        } else {
            document.addEventListener('alpine:init', initUserPage);
        }
    </script>
</x-app-layout>