<?php

namespace App\View\Components\Layout;

use App\Services\OrganizationProfileService;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class Header extends Component
{
    public string $pageTitle;
    public ?string $financialPeriod = null;

    public function __construct(?string $pageTitle = null)
    {
        $this->pageTitle = $pageTitle ?? $this->resolveTitleFromRoute();

        try {
            $profileService = app(OrganizationProfileService::class);
            $profile = $profileService->getProfile();
            if ($profile && $profile->financial_period_start && $profile->financial_period_end) {
                $this->financialPeriod = $profile->financial_period_start->format('d/m/Y') . ' s.d ' . $profile->financial_period_end->format('d/m/Y');
            }
        } catch (\Throwable $e) {
            $this->financialPeriod = null;
        }
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
            'general-ledger.index' => 'Buku Besar (General Ledger)',
            'coa.index' => 'Daftar Chart of Accounts',
            'receipts.index' => 'Penerimaan Kas',
            default => 'Dasbor Utama',
        };
    }
}