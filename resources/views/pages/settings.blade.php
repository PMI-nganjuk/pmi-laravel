<x-app-layout>
    <x-layout.shell
        page-title="Profil Organisasi"
        x-data="settingsPage"
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

            <x-atoms.surface
                tag="section"
                aria-labelledby="settings-form-title"
            >
                <div class="mb-6 border-b border-surface-border pb-5">
                    <h2 id="settings-form-title" class="text-lg font-bold text-content-base">Profil Organisasi</h2>
                    <p class="mt-1 text-sm text-content-muted">Perbarui profil dan periode buku organisasi PMI.</p>
                </div>

                <form
                    x-ref="settingsForm"
                    action="{{ route('settings.update') }}"
                    method="POST"
                    x-on:submit.prevent="submitForm()"
                    class="space-y-6"
                    novalidate
                >
                    @csrf
                    @method('PUT')

                    <!-- Section: Informasi Umum -->
                    <div>
                        <h3 class="text-sm font-bold text-content-base border-l-4 border-primary pl-2 mb-4">Informasi Umum</h3>
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <x-atoms.input
                                name="organization_name"
                                label="Nama Entitas"
                                x-model="organizationName"
                                placeholder="Masukkan nama entitas/organisasi"
                            />
                            
                            <div class="md:col-span-2">
                                <label for="address" class="mb-2 block text-xs font-bold uppercase tracking-normal text-content-base">
                                    Alamat
                                </label>
                                <textarea
                                    name="address"
                                    id="address"
                                    rows="3"
                                    placeholder="Masukkan alamat lengkap entitas"
                                    class="block w-full rounded-xl border bg-surface-base text-sm text-content-base transition duration-200 placeholder:text-content-subtle hover:border-content-subtle focus-visible:border-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-ring disabled:cursor-not-allowed disabled:bg-surface-muted disabled:opacity-60 px-4 py-3 border-surface-border"
                                    x-model="address"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Pengurus -->
                    <div class="pt-4 border-t border-surface-border">
                        <h3 class="text-sm font-bold text-content-base border-l-4 border-primary pl-2 mb-4">Pengurus</h3>
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                            <x-atoms.input as="select" name="chairperson" label="Ketua" x-model="chairperson">
                                <option value="">-- Pilih Ketua --</option>
                                @foreach ($userNames as $userName)
                                    <option value="{{ $userName }}">{{ $userName }}</option>
                                @endforeach
                            </x-atoms.input>

                            <x-atoms.input as="select" name="headquarters_treasurer" label="Bendahara Markas" x-model="headquartersTreasurer">
                                <option value="">-- Pilih Bendahara Markas --</option>
                                @foreach ($userNames as $userName)
                                    <option value="{{ $userName }}">{{ $userName }}</option>
                                @endforeach
                            </x-atoms.input>

                            <x-atoms.input as="select" name="blood_donation_unit_treasurer" label="Bendahara UDD" x-model="bloodDonationUnitTreasurer">
                                <option value="">-- Pilih Bendahara UDD --</option>
                                @foreach ($userNames as $userName)
                                    <option value="{{ $userName }}">{{ $userName }}</option>
                                @endforeach
                            </x-atoms.input>
                        </div>
                    </div>

                    <!-- Section: Manual Book -->
                    <div class="pt-4 border-t border-surface-border">
                        <h3 class="text-sm font-bold text-content-base border-l-4 border-primary pl-2 mb-4">Manual Book</h3>
                        <div class="bg-surface-base border border-surface-border rounded-xl p-6 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
                            <div>
                                <h4 class="font-bold text-content-base">Panduan Penggunaan Aplikasi</h4>
                                <p class="text-sm text-content-muted mt-1">Akses dokumen panduan lengkap untuk mempelajari cara penggunaan sistem keuangan ini.</p>
                            </div>
                            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                                <x-atoms.button 
                                    type="button" 
                                    variant="outline" 
                                    size="md" 
                                    @click="navigator.clipboard.writeText('#'); alert('Link Manual Book berhasil disalin!')"
                                    class="w-full sm:w-auto justify-center"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                    </svg>
                                    Salin Link
                                </x-atoms.button>

                                <a href="#" target="_blank" class="w-full sm:w-auto">
                                    <x-atoms.button 
                                        type="button" 
                                        variant="info" 
                                        size="md" 
                                        class="w-full justify-center"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        Buka Manual Book
                                    </x-atoms.button>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-5 border-t border-surface-border flex justify-end">
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
                            <span x-show="!loadingSubmit">Simpan Perubahan</span>
                        </x-atoms.button>
                    </div>
                </form>
            </x-atoms.surface>
        </main>
    </x-layout.shell>

    <script>
        function initSettingsPage() {
            Alpine.data('settingsPage', () => ({
                sidebarOpen: false,
                organizationName: @js(old('organization_name', $profile->organization_name ?? '')),
                address: @js(old('address', $profile->address ?? '')),
                chairperson: @js(old('chairperson', $profile->chairperson ?? '')),
                headquartersTreasurer: @js(old('headquarters_treasurer', $profile->headquarters_treasurer ?? '')),
                bloodDonationUnitTreasurer: @js(old('blood_donation_unit_treasurer', $profile->blood_donation_unit_treasurer ?? '')),
                loadingSubmit: false,

                submitForm() {
                    this.loadingSubmit = true;
                    this.$refs.settingsForm.submit();
                }
            }));
        }

        if (window.Alpine) {
            initSettingsPage();
        } else {
            document.addEventListener('alpine:init', initSettingsPage);
        }
    </script>
</x-app-layout>
