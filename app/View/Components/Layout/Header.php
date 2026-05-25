<?php

namespace App\View\Components\Layout;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class Header extends Component
{
    public string $pageTitle;

    public function __construct(?string $pageTitle = null)
    {
        $this->pageTitle = $pageTitle ?? $this->resolveTitleFromRoute();
    }

    public function render()
    {
        return view('components.layout.header', [
            'user' => Auth::user(),
        ]);
    }

    private function resolveTitleFromRoute(): string
    {
        $routeName = Route::currentRouteName();
        
        return match($routeName) {
            'dashboard' => 'Dashboard Ringkasan',
            'settings.index' => 'Konfigurasi Sistem',
            'users.index' => 'Manajemen Akun',
            'finance.journal' => 'Jurnal Keuangan',
            'coa.index' => 'Daftar Chart of Accounts',
            default => 'Dasbor Utama',
        };
    }
}