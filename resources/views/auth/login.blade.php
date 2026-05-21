<x-app-layout>
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50 font-sans text-slate-900 antialiased">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <!-- Brand Logo / Badge -->
            <div class="inline-flex items-center justify-center p-3 bg-red-50 border border-red-200 rounded-full mb-4">
                <svg class="h-10 w-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900">
                PMI Nganjuk
            </h2>
            <p class="mt-2 text-sm text-slate-500">
                Sistem Manajemen Keuangan Terintegrasi
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
            <!-- Accessible Card -->
            <div class="bg-white py-8 px-4 border border-slate-200 rounded-2xl shadow-sm sm:px-10">
                
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-xl" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700">
                            Alamat Email
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                value="{{ old('email') }}"
                                class="appearance-none block w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl shadow-sm placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition duration-200"
                                placeholder="nama@pmi-nganjuk.or.id">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700">
                            Kata Sandi
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="appearance-none block w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl shadow-sm placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition duration-200"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" 
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-slate-350 rounded transition duration-200">
                            <label for="remember" class="ml-2 block text-sm text-slate-650 font-medium">
                                Ingat Saya
                            </label>
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                            Masuk Ke Sistem
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Quick Fill Demo Accounts Card -->
            <div class="mt-6 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm" x-data="{
                accounts: [
                    { label: 'Admin', email: 'admin@pmi-nganjuk.or.id' },
                    { label: 'Manager Keuangan', email: 'manager@pmi-nganjuk.or.id' },
                    { label: 'Staf Keuangan', email: 'stafkeuangan@pmi-nganjuk.or.id' },
                    { label: 'Karyawan', email: 'karyawan@pmi-nganjuk.or.id' },
                    { label: 'Pengguna Umum', email: 'pengguna@pmi-nganjuk.or.id' }
                ],
                fill(email) {
                    document.getElementById('email').value = email;
                    document.getElementById('password').value = 'password';
                }
            }">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center">
                    <svg class="h-4 w-4 mr-1.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Akses Cepat Demo Akun
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <template x-for="acc in accounts" :key="acc.email">
                        <button type="button" @click="fill(acc.email)"
                            class="flex flex-col items-start p-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-slate-350 rounded-xl transition duration-155 text-left group">
                            <span class="text-xs font-bold text-slate-800" x-text="acc.label"></span>
                            <span class="text-[10px] text-slate-500 overflow-hidden truncate max-w-full" x-text="acc.email"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
