<x-app-layout>
    <x-layout.shell page-title="Edit Akun Pengguna">
        <main class="flex-1 overflow-y-auto p-6">
            <div class="max-w-3xl mx-auto w-full space-y-6">
                <!-- Back Link -->
                <div class="mb-4">
                    <a href="{{ route('users.index') }}" class="inline-flex items-center text-xs font-semibold text-content-muted hover:text-content-base transition duration-200 gap-1.5">
                        <x-atoms.icon name="arrow-left" class="h-4 w-4" />
                        Kembali ke Daftar Pengguna
                    </a>
                </div>

                <!-- Edit Card Form -->
                <x-atoms.surface class="p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-content-base mb-1">Formulir Edit Akun</h2>
                    <p class="text-content-muted text-xs mb-6">Perbarui informasi akun pengguna dan peran sistemnya.</p>

                    @if ($errors->any())
                        <x-atoms.alert variant="danger" class="mb-6">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-atoms.alert>
                    @endif

                    <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <!-- Name field -->
                        <x-atoms.input
                            name="name"
                            label="Nama Lengkap"
                            required
                            value="{{ old('name', $user->name) }}"
                            placeholder="Masukkan nama lengkap"
                        />

                        <!-- Email field -->
                        <x-atoms.input
                            name="email"
                            type="email"
                            label="Alamat Email"
                            required
                            value="{{ old('email', $user->email) }}"
                            placeholder="nama@pmi-nganjuk.or.id"
                        />

                        <!-- Role field selection -->
                        <x-atoms.input as="select" name="role" label="Peran Sistem (Role)" required>
                            @foreach ($roles as $r)
                                <option value="{{ $r->value }}" {{ old('role', $user->role->value) === $r->value ? 'selected' : '' }}>
                                    {{ $r->getLabel() }}
                                </option>
                            @endforeach
                        </x-atoms.input>

                        <!-- Password field -->
                        <x-atoms.input
                            name="password"
                            type="password"
                            label="Kata Sandi Baru (Kosongkan jika tidak ingin mengubah)"
                            placeholder="Minimal 8 karakter, kosongkan jika tidak diubah"
                        />

                        <!-- Action buttons -->
                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-surface-border mt-6">
                            <x-atoms.button
                                as="a"
                                :href="route('users.index')"
                                variant="secondary"
                                size="md"
                            >
                                Batal
                            </x-atoms.button>
                            <x-atoms.button
                                type="submit"
                                variant="primary"
                                size="md"
                            >
                                Simpan Perubahan
                            </x-atoms.button>
                        </div>
                    </form>
                </x-atoms.surface>
            </div>
        </main>
    </x-layout.shell>
</x-app-layout>
