<?php

namespace App\Services;

use App\Repositories\ProfitLossRepository;
use Illuminate\Support\Collection;

class ProfitLossService
{
    private const CATEGORY_PENDAPATAN           = 'Pendapatan';
    private const CATEGORY_BEBAN_PROGRAM        = 'Beban Program';
    private const CATEGORY_BEBAN_MANAJEMEN_UMUM = 'Beban Manajemen Umum';

    public function __construct(
        protected ProfitLossRepository $repository
    ) {}

    public function generateReport(?string $startDate = null, ?string $endDate = null): array
    {
        $period = $this->repository->getFinancialPeriod();
        $start  = $startDate ?? $period['start'];
        $end    = $endDate ?? $period['end'];

        $balances = $this->repository->getAccountBalances($start, $end);
        $grouped = $this->groupByHierarchy($balances);

        $pendapatan         = $this->buildSection($grouped, self::CATEGORY_PENDAPATAN);
        $bebanProgram       = $this->buildSection($grouped, self::CATEGORY_BEBAN_PROGRAM);
        $bebanManajemenUmum = $this->buildSection($grouped, self::CATEGORY_BEBAN_MANAJEMEN_UMUM);

        $totalPendapatan = $this->sumSectionTotals($pendapatan);
        $totalBebanProgram = $this->sumSectionTotals($bebanProgram);
        $totalBebanManajemenUmum = $this->sumSectionTotals($bebanManajemenUmum);

        $totalBeban = [
            'tidak_terikat' => $totalBebanProgram['tidak_terikat'] + $totalBebanManajemenUmum['tidak_terikat'],
            'terikat'       => $totalBebanProgram['terikat'] + $totalBebanManajemenUmum['terikat'],
            'total'         => $totalBebanProgram['total'] + $totalBebanManajemenUmum['total'],
        ];

        $surplus = [
            'tidak_terikat' => $totalPendapatan['tidak_terikat'] - $totalBeban['tidak_terikat'],
            'terikat'       => $totalPendapatan['terikat'] - $totalBeban['terikat'],
            'total'         => $totalPendapatan['total'] - $totalBeban['total'],
        ];

        return [
            'period'               => ['start' => $start, 'end' => $end],
            'pendapatan'           => $pendapatan,
            'beban_program'        => $bebanProgram,
            'beban_manajemen_umum' => $bebanManajemenUmum,
            'total_pendapatan'     => $totalPendapatan,
            'total_beban_program'  => $totalBebanProgram,
            'total_beban_manajemen_umum' => $totalBebanManajemenUmum,
            'total_beban'          => $totalBeban,
            'surplus'              => $surplus,
        ];
    }

    public function getPageData(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? null;
        $endDate   = $filters['end_date'] ?? null;

        return [
            'report' => $this->generateReport($startDate, $endDate),
        ];
    }

    private function groupByHierarchy(Collection $balances): array
    {
        $result = [];

        foreach ($balances as $row) {
            $category    = $row->category_name;
            $subcategory = $row->subcategory_name;

            if (!isset($result[$category])) {
                $result[$category] = [];
            }
            if (!isset($result[$category][$subcategory])) {
                $result[$category][$subcategory] = collect();
            }

            $result[$category][$subcategory]->push($row);
        }

        return $result;
    }

    private function buildSection(array $grouped, string $categoryName): array
    {
        if (!isset($grouped[$categoryName])) {
            return [];
        }

        $sections = [];

        foreach ($grouped[$categoryName] as $subcategoryName => $accounts) {
            $accountRows = [];
            $subtotal = ['tidak_terikat' => 0, 'terikat' => 0, 'total' => 0];

            foreach ($accounts as $account) {
                $accountRows[] = [
                    'kode'          => $account->chart_of_account_id,
                    'nama'          => $account->account_name,
                    'tidak_terikat' => (float) $account->tidak_terikat,
                    'terikat'       => (float) $account->terikat,
                    'total'         => (float) $account->total,
                ];

                $subtotal['tidak_terikat'] += (float) $account->tidak_terikat;
                $subtotal['terikat']       += (float) $account->terikat;
                $subtotal['total']         += (float) $account->total;
            }

            $sections[] = [
                'name'     => $subcategoryName,
                'accounts' => $accountRows,
                'subtotal' => $subtotal,
            ];
        }

        return $sections;
    }

    private function sumSectionTotals(array $sections): array
    {
        $total = ['tidak_terikat' => 0, 'terikat' => 0, 'total' => 0];

        foreach ($sections as $section) {
            $total['tidak_terikat'] += $section['subtotal']['tidak_terikat'];
            $total['terikat']       += $section['subtotal']['terikat'];
            $total['total']         += $section['subtotal']['total'];
        }

        return $total;
    }
}
