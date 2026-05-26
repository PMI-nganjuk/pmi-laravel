<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>PMI Kabupaten Nganjuk - Sistem Manajemen Keuangan</title>
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
        </style>
    </head>
    <body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col justify-between">
        <!-- Navigation Header -->
        <header class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 bg-red-50 border border-red-200 rounded-lg">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo PMI" class="h-6 w-6 object-contain" />
                    </div>
                    <span class="font-bold text-lg text-slate-900 tracking-wider">PMI Nganjuk</span>
                </div>
                
                <div>
                    @if (Route::has('login'))
                        <nav class="flex items-center gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-sm font-semibold text-white rounded-xl transition duration-200 shadow-sm">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-sm font-semibold text-white rounded-xl transition duration-200 shadow-sm">
                                    Masuk ke Sistem
                                </a>
                            @endauth
                        </nav>
                    @endif
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1 flex items-center justify-center py-16 px-6">
            <div class="max-w-3xl text-center space-y-6">
                <span class="inline-flex items-center gap-1.5 text-xs text-red-750 font-bold bg-red-50 px-3 py-1.5 border border-red-200 rounded-full uppercase tracking-wider">
                    Sistem Internal
                </span>
                
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Sistem Manajemen Keuangan & Peran Terpusat
                </h1>
                
                <p class="text-slate-600 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
                    Selamat datang di portal manajemen keuangan Palang Merah Indonesia (PMI) Kabupaten Nganjuk. Sistem ini dirancang untuk memudahkan otorisasi transaksi, pencatatan jurnal keuangan, dan pembuatan laporan secara aman, transparan, dan efisien.
                </p>

                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-3.5 bg-red-600 hover:bg-red-700 text-sm font-bold text-white rounded-xl transition duration-200 shadow-md">
                            Buka Dashboard Ringkasan
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 bg-red-600 hover:bg-red-700 text-sm font-bold text-white rounded-xl transition duration-200 shadow-md">
                            Masuk Ke Portal Akun
                        </a>
                    @endauth
                    <a href="https://pmi-nganjuk.or.id" target="_blank" class="w-full sm:w-auto px-8 py-3.5 bg-white hover:bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-700 rounded-xl transition duration-200">
                        Kunjungi Situs Web Resmi
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <span>&copy; {{ date('Y') }} Palang Merah Indonesia Kabupaten Nganjuk. Hak Cipta Dilindungi.</span>
                <span>Sistem Otentikasi & Otorisasi Terenkripsi</span>
            </div>
        </footer>
    </body>
</html>
