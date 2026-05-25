<?php

namespace App\View\Components\Layout;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use App\Enums\RoleEnum;

class Sidebar extends Component
{
    public function render()
    {
        return view('components.layout.sidebar', [
            'menuItems' => $this->getAuthorizedMenu(),
            'user' => Auth::user()
        ]);
    }

    private function getAuthorizedMenu(): array
    {
        $user = Auth::user();
        $menu = [];

        // General Menu
        $menu[] = [
            'label' => 'Ringkasan',
            'route' => 'dashboard',
            'icon'  => 'home',
            'active'=> request()->routeIs('dashboard')
        ];

        $menu[] = [
            'label' => 'Program Kerja',
            'route' => 'programs.index',
            'icon'  => 'briefcase',
            'active'=> request()->routeIs('programs.*')
        ];

        // Admin Only
        if ($user->hasRole(RoleEnum::ADMIN)) {
            $menu[] = [
                'label' => 'Konfigurasi Sistem',
                'route' => 'settings.index',
                'icon'  => 'cog',
                'active'=> request()->routeIs('settings.*')
            ];
            $menu[] = [
                'label' => 'Manajemen Akun',
                'route' => 'users.index',
                'icon'  => 'users',
                'active'=> request()->routeIs('users.*')
            ];
        }

        // Finance Domain
        if ($user->hasAnyRole([RoleEnum::ADMIN, RoleEnum::FINANCIAL_MANAGER, RoleEnum::FINANCE_STAFF])) {
            $menu[] = [
                'label' => 'Jurnal Keuangan',
                'route' => 'finance.journal',
                'icon'  => 'document',
                'active'=> request()->routeIs('finance.journal')
            ];
            $menu[] = [
                'label' => 'Laporan Finansial',
                'route' => 'finance.reports',
                'icon'  => 'chart',
                'active'=> request()->routeIs('finance.reports')
            ];
            $menu[] = [
                'label' => 'Chart of Accounts',
                'route' => 'coa.index',
                'icon'  => 'book',
                'active'=> request()->routeIs('coa.*')
            ];
        }

        return $menu;
    }
}