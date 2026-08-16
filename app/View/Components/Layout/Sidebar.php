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
        if (!$user) {
            return [];
        }

        $sections = [];

        // 1. Menu Utama (Non-collapsible)
        $mainMenu = [
            [
                'label' => 'Ringkasan',
                'route' => 'dashboard',
                'icon'  => 'home',
                'active'=> request()->routeIs('dashboard')
            ],
        ];

        $sections[] = [
            'key'         => 'utama',
            'label'       => 'Menu Utama',
            'collapsible' => false,
            'default_open'=> true,
            'items'       => $mainMenu
        ];

        // 2. Keuangan (Collapsible)
        if ($user->hasAnyRole([RoleEnum::ADMIN, RoleEnum::FINANCE_STAFF])) {
            $financeMenu = [
                [
                    'label' => 'Penerimaan Kas',
                    'route' => 'receipts.index',
                    'icon'  => 'money-add',
                    'active'=> request()->routeIs('receipts.*')
                ],
                [
                    'label' => 'Pengeluaran Kas',
                    'route' => 'disbursements.index',
                    'icon'  => 'money-send',
                    'active'=> request()->routeIs('disbursements.*')
                ],
                [
                    'label' => 'Jurnal Penyesuaian',
                    'route' => 'adjusting-entries.index',
                    'icon'  => 'money-add',
                    'active'=> request()->routeIs('adjusting-entries.*')
                ],
                [
                    'label' => 'Chart of Accounts',
                    'route' => 'coa.index',
                    'icon'  => 'book',
                    'active'=> request()->routeIs('coa.*')
                ],
            ];

            // Section active status if any item inside is active
            $isFinanceActive = false;
            foreach ($financeMenu as $item) {
                if ($item['active']) {
                    $isFinanceActive = true;
                    break;
                }
            }

            $sections[] = [
                'key'         => 'keuangan',
                'label'       => 'Input Keuangan',
                'collapsible' => true,
                'default_open'=> $isFinanceActive,
                'items'       => $financeMenu
            ];
        }

        $report = [
            [
                'label' => 'Buku Besar',
                'route' => 'general-ledger.index',
                'icon'  => 'book',
                'active'=> request()->routeIs('general-ledger.*')
            ],
            [
                'label' => 'Laporan Laba Rugi',
                'route' => 'profit-loss.index',
                'icon'  => 'chart',
                'active'=> request()->routeIs('profit-loss.*')
            ],
            [
                'label' => 'Laporan Posisi Keuangan',
                'route' => 'balance-sheet.index',
                'icon'  => 'chart',
                'active'=> request()->routeIs('balance-sheet.*')
            ],
            [
                'label' => 'Laporan Arus Kas',
                'route' => 'cash-flow.index',
                'icon'  => 'chart',
                'active'=> request()->routeIs('cash-flow.*')
            ],
            [
                'label' => 'Laporan Perubahan Aset Netto',
                'route' => 'analysis-notes.index',
                'icon'  => 'chart',
                'active'=> request()->routeIs('analysis-notes.*')
            ],
        ];

        $isReportActive = false;
        foreach ($report as $item) {
            if ($item['active']) {
                $isReportActive = true;
                break;
            }
        }

        $isSystemActive = false;
        if ($user->hasRole(RoleEnum::ADMIN)) {
            $systemMenu = [
                [
                    'label' => 'Profil Organisasi',
                    'route' => 'settings.index',
                    'icon'  => 'cog',
                    'active'=> request()->routeIs('settings.*')
                ],
                [
                    'label' => 'Manajemen Akun',
                    'route' => 'users.index',
                    'icon'  => 'users',
                    'active'=> request()->routeIs('users.*')
                ],
                [
                    'label' => 'Program Kerja',
                    'route' => 'programs.index',
                    'icon'  => 'briefcase',
                    'active'=> request()->routeIs('programs.*')
                ],
            ];

            foreach ($systemMenu as $item) {
                if ($item['active']) {
                    $isSystemActive = true;
                    break;
                }
            }
        }

        $sections[] = [
            'key'         => 'laporan',
            'label'       => 'Laporan Keuangan',
            'collapsible' => true,
            'default_open'=> $isReportActive || (!$isFinanceActive && !$isSystemActive),
            'items'       => $report
        ];

        // 3. Konfigurasi Sistem (Collapsible)
        if ($user->hasRole(RoleEnum::ADMIN) && isset($systemMenu)) {
            $sections[] = [
                'key'         => 'sistem',
                'label'       => 'Manajemen Organisasi',
                'collapsible' => true,
                'default_open'=> $isSystemActive,
                'items'       => $systemMenu
            ];
        }

        return $sections;
    }
}