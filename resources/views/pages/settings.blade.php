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
                    <p class="mt-1 text-sm text-content-muted">Perbarui profil dan pengurus organisasi PMI.</p>
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
                        <div class="bg-surface-base border border-surface-border rounded-xl p-6 shadow-sm">
                            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-5 pb-5 border-b border-slate-100">
                                <div>
                                    <h4 class="font-bold text-content-base">Panduan Penggunaan Aplikasi</h4>
                                    <p class="text-sm text-content-muted mt-1">Unduh dokumen panduan lengkap untuk mempelajari cara penggunaan sistem keuangan ini.</p>
                                </div>
                                <x-atoms.button 
                                    type="button" 
                                    variant="primary" 
                                    size="md" 
                                    @click="showManualBookModal = true"
                                    class="w-full sm:w-auto justify-center"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Kelola Manual Book
                                </x-atoms.button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Box Google Drive -->
                                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 flex flex-col justify-between">
                                    <div class="mb-4">
                                        <div class="flex items-center gap-2 text-slate-800 font-bold mb-1">
                                            <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                                            Penyimpanan Cloud
                                        </div>
                                        <p class="text-xs text-slate-500">Akses atau salin link Google Drive untuk panduan manual book.</p>
                                    </div>
                                    <div class="flex flex-col sm:flex-row items-center gap-2">
                                        <x-atoms.button type="button" variant="outline" size="sm" @click="navigator.clipboard.writeText('{{ $profile->manual_book_link ?? '' }}'); alert('Link Google Drive berhasil disalin!')" class="w-full sm:w-auto justify-center bg-white">
                                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg> Salin Link
                                        </x-atoms.button>
                                        <a href="{{ $profile->manual_book_link ?? '#' }}" target="_blank" class="w-full sm:w-auto">
                                            <x-atoms.button type="button" variant="outline" size="sm" class="w-full justify-center bg-white text-blue-600 border-blue-200 hover:bg-blue-50">
                                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg> Buka GDrive
                                            </x-atoms.button>
                                        </a>
                                    </div>
                                </div>

                                <!-- Box Unduhan Lokal -->
                                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 flex flex-col justify-between">
                                    <div class="mb-4">
                                        <div class="flex items-center gap-2 text-slate-800 font-bold mb-1">
                                            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            Unduhan Langsung
                                        </div>
                                        <p class="text-xs text-slate-500">Unduh dokumen manual book yang tersimpan di sistem.</p>
                                    </div>
                                    <div class="flex flex-col sm:flex-row items-center gap-2">
                                        <a href="{{ route('manual-book.download-docx') }}?v={{ time() }}" target="_blank" class="w-full sm:w-auto">
                                            <x-atoms.button type="button" variant="outline" size="sm" class="w-full justify-center bg-white text-emerald-600 border-emerald-200 hover:bg-emerald-50">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg> DOCX
                                            </x-atoms.button>
                                        </a>
                                        <a href="{{ route('manual-book.download') }}?v={{ time() }}" target="_blank" class="w-full sm:w-auto">
                                            <x-atoms.button type="button" variant="outline" size="sm" class="w-full justify-center bg-white text-red-600 border-red-200 hover:bg-red-50">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> PDF
                                            </x-atoms.button>
                                        </a>
                                    </div>
                                </div>
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
            <!-- Modal Kelola Manual Book -->
            <div 
                x-show="showManualBookModal" 
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                style="display: none;"
            >
                <div 
                    class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden"
                    @click.away="showManualBookModal = false"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-900">Kelola Manual Book</h3>
                        <button @click="showManualBookModal = false" class="text-slate-400 hover:text-slate-500">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    @php
                        $pdfExists = file_exists(public_path('manual-book/Manual_Book_PMI_Nganjuk.pdf'));
                        $docxExists = file_exists(public_path('manual-book/Manual_Book_PMI_Nganjuk.docx'));
                    @endphp
                    <form action="{{ route('manual-book.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                        @csrf
                        
                        <!-- Link GDrive -->
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-2">Tautan Google Drive</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                </div>
                                <input type="url" name="manual_book_link" value="{{ $profile->manual_book_link ?? '' }}" placeholder="https://drive.google.com/..." class="w-full rounded-xl border border-slate-300 pl-11 pr-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500 transition-colors bg-slate-50 focus:bg-white">
                            </div>
                            <p class="text-xs text-slate-500 mt-1.5">Kosongkan jika tidak ingin menggunakan Google Drive.</p>
                        </div>

                        <hr class="border-slate-100">

                        <div class="space-y-6">
                            <h4 class="text-sm font-bold text-slate-800">Dokumen Unduhan Lokal</h4>

                            <!-- File PDF -->
                            <div class="border border-slate-200 rounded-xl p-4 transition-colors hover:border-slate-300 bg-slate-50/50">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="p-2 bg-red-100 rounded-lg text-red-600">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-slate-800">Versi PDF (.pdf)</label>
                                            @if($pdfExists)
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Saat ini tersedia
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-500">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Belum ada file
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <input type="file" name="manual_book_pdf" accept=".pdf" class="block w-full text-sm text-slate-500 file:cursor-pointer file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-white file:border file:border-slate-300 file:text-slate-700 hover:file:bg-slate-50 transition-colors">
                                <p class="text-xs text-slate-500 mt-2">Pilih file baru untuk <strong>menimpa</strong> versi yang lama (Maks: 20MB).</p>
                            </div>

                            <!-- File DOCX -->
                            <div class="border border-slate-200 rounded-xl p-4 transition-colors hover:border-slate-300 bg-slate-50/50">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-slate-800">Versi Word (.docx)</label>
                                            @if($docxExists)
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Saat ini tersedia
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-500">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Belum ada file
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <input type="file" name="manual_book_docx" accept=".docx,.doc" class="block w-full text-sm text-slate-500 file:cursor-pointer file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-white file:border file:border-slate-300 file:text-slate-700 hover:file:bg-slate-50 transition-colors">
                                <p class="text-xs text-slate-500 mt-2">Pilih file baru untuk <strong>menimpa</strong> versi yang lama (Maks: 20MB).</p>
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-6">
                            <button type="button" @click="showManualBookModal = false" class="px-6 py-3 text-sm font-bold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">Batal</button>
                            <button type="submit" class="px-6 py-3 text-sm font-bold text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </x-layout.shell>

    <script>
        function initSettingsPage() {
            Alpine.data('settingsPage', () => ({
                sidebarOpen: false,
                showManualBookModal: false,
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
