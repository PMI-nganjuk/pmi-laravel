@props(['title' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Manajemen Keuangan PMI Kabupaten Nganjuk">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ? $title . ' - PMI Nganjuk' : (config('app.name') && config('app.name') !== 'Laravel' ? config('app.name') : 'PMI Nganjuk - Sistem Manajemen Keuangan') }}</title>

    <!-- Favicon PMI -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-slate-900 antialiased">
    <div class="min-h-screen">
        {{ $slot }}
    </div>
</body>
</html>
