<x-app-layout title="Masuk">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50 font-sans text-slate-900 antialiased">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <!-- Brand Logo / Badge -->
            <div class="inline-flex items-center justify-center p-3 bg-red-50 border border-red-200 rounded-full mb-4">
                <img src="{{ asset('images/logo.svg') }}" class="h-10 w-10 object-contain" alt="Logo PMI">
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
        </div>
    </div>
</x-app-layout>