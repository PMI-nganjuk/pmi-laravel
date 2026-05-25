<x-app-layout>
    <x-layout.shell page-title="Profil Saya" x-data="profilePage">

        <main class="flex-1 overflow-y-auto p-6 space-y-6">

            @if (session('success'))
                <x-atoms.alert variant="success">{{ session('success') }}</x-atoms.alert>
            @endif

            @if (session('error'))
                <x-atoms.alert variant="danger">{{ session('error') }}</x-atoms.alert>
            @endif

            {{-- ── Avatar & Nama ─────────────────────────────────────────── --}}
            <x-atoms.surface tag="div" class="flex items-center gap-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-normal text-primary">Akun Aktif</p>
                    <h2 class="mt-0.5 text-lg font-bold text-content-base">{{ $user->name }}</h2>
                    <p class="text-sm text-content-muted">{{ $user->role->getLabel() }}</p>
                </div>
            </x-atoms.surface>

            {{-- ── Form Identitas ────────────────────────────────────────── --}}
            <x-atoms.surface tag="section" aria-labelledby="info-title">
                <div class="mb-6 border-b border-surface-border pb-5">
                    <p class="text-xs font-bold uppercase tracking-normal text-primary">Identitas</p>
                    <h2 id="info-title" class="mt-1 text-lg font-bold text-content-base">Informasi Akun</h2>
                    <p class="mt-1 text-sm text-content-muted">Perbarui nama dan alamat email akun Anda.</p>
                </div>

                @if ($errors->infoForm->any())
                    <x-atoms.alert variant="danger" class="mb-4">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->infoForm->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-atoms.alert>
                @endif

                <form action="{{ route('profile.update-info') }}" method="POST"
                      class="grid grid-cols-1 gap-5 md:grid-cols-2" novalidate>
                    @csrf
                    @method('PUT')

                    <x-atoms.input
                        name="name"
                        label="Nama Lengkap"
                        required
                        :value="old('name', $user->name)"
                        placeholder="Masukkan nama lengkap"
                    />

                    <x-atoms.input
                        name="email"
                        type="email"
                        label="Alamat Email"
                        required
                        :value="old('email', $user->email)"
                        placeholder="nama@pmi-nganjuk.or.id"
                    />

                    <div class="mt-2 flex justify-end border-t border-surface-border pt-5 md:col-span-2">
                        <x-atoms.button type="submit" variant="primary" size="md">
                            Simpan Perubahan
                        </x-atoms.button>
                    </div>
                </form>
            </x-atoms.surface>

            {{-- ── Form Kata Sandi ───────────────────────────────────────── --}}
            <x-atoms.surface tag="section" aria-labelledby="password-title">
                <div class="mb-6 border-b border-surface-border pb-5">
                    <p class="text-xs font-bold uppercase tracking-normal text-primary">Keamanan</p>
                    <h2 id="password-title" class="mt-1 text-lg font-bold text-content-base">Ubah Kata Sandi</h2>
                    <p class="mt-1 text-sm text-content-muted">Gunakan kata sandi yang kuat dan unik untuk menjaga keamanan akun.</p>
                </div>

                @if ($errors->passwordForm->any())
                    <x-atoms.alert variant="danger" class="mb-4">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->passwordForm->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-atoms.alert>
                @endif

                <form action="{{ route('profile.update-password') }}" method="POST"
                      class="grid grid-cols-1 gap-5 md:grid-cols-2" novalidate>
                    @csrf
                    @method('PUT')

                    <x-atoms.input
                        name="password"
                        type="password"
                        label="Kata Sandi Baru"
                        required
                        placeholder="Minimal 8 karakter"
                    />

                    <x-atoms.input
                        name="password_confirmation"
                        type="password"
                        label="Konfirmasi Kata Sandi Baru"
                        required
                        placeholder="Ulangi kata sandi baru"
                    />

                    <div class="mt-2 flex justify-end border-t border-surface-border pt-5 md:col-span-2">
                        <x-atoms.button type="submit" variant="primary" size="md">
                            Ubah Kata Sandi
                        </x-atoms.button>
                    </div>
                </form>
            </x-atoms.surface>

        </main>
    </x-layout.shell>

    <script>
        function initProfilePage() {
            Alpine.data('profilePage', () => ({
                sidebarOpen: false,
            }));
        }
        if (window.Alpine) {
            initProfilePage();
        } else {
            document.addEventListener('alpine:init', initProfilePage);
        }
    </script>
</x-app-layout>
