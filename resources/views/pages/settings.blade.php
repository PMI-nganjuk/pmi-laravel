<x-app-layout>
    <x-layout.shell
        page-title="Konfigurasi Sistem"
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
                            <x-atoms.input
                                name="chairperson"
                                label="Ketua"
                                x-model="chairperson"
                                placeholder="Nama Ketua"
                            />
                            
                            <x-atoms.input
                                name="headquarters_treasurer"
                                label="Bendahara Markas"
                                x-model="headquartersTreasurer"
                                placeholder="Nama Bendahara Markas"
                            />

                            <x-atoms.input
                                name="blood_donation_unit_treasurer"
                                label="Bendahara UDD"
                                x-model="bloodDonationUnitTreasurer"
                                placeholder="Nama Bendahara UDD"
                            />
                        </div>
                    </div>

                    <!-- Section: Periode Buku -->
                    <div class="pt-4 border-t border-surface-border">
                        <h3 class="text-sm font-bold text-content-base border-l-4 border-primary pl-2 mb-4">Periode Buku</h3>
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                            <x-atoms.input
                                name="financial_period_start"
                                type="date"
                                label="Periode Awal"
                                x-model="financialPeriodStart"
                                x-on:change="updateFiscalYear()"
                            />
                            
                            <x-atoms.input
                                name="financial_period_end"
                                type="date"
                                label="Periode Akhir"
                                x-model="financialPeriodEnd"
                            />

                            <x-atoms.input
                                name="fiscal_year"
                                type="number"
                                label="Tahun Buku"
                                x-model="fiscalYear"
                                placeholder="Tahun"
                            />
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
                organizationName: @js(old('organization_name', $profile->organization_name ?? '')),
                address: @js(old('address', $profile->address ?? '')),
                chairperson: @js(old('chairperson', $profile->chairperson ?? '')),
                headquartersTreasurer: @js(old('headquarters_treasurer', $profile->headquarters_treasurer ?? '')),
                bloodDonationUnitTreasurer: @js(old('blood_donation_unit_treasurer', $profile->blood_donation_unit_treasurer ?? '')),
                financialPeriodStart: @js(old('financial_period_start', $profile->financial_period_start ? $profile->financial_period_start->format('Y-m-d') : '')),
                financialPeriodEnd: @js(old('financial_period_end', $profile->financial_period_end ? $profile->financial_period_end->format('Y-m-d') : '')),
                fiscalYear: @js(old('fiscal_year', $profile->fiscal_year ?? '')),
                loadingSubmit: false,

                updateFiscalYear() {
                    if (this.financialPeriodStart) {
                        const date = new Date(this.financialPeriodStart);
                        if (!isNaN(date.getTime())) {
                            this.fiscalYear = date.getFullYear();
                        }
                    }
                },

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
