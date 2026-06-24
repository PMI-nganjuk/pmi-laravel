<?php

namespace App\Repositories;

use App\Services\OrganizationProfileService;
use Illuminate\Support\Facades\DB;

class AnalysisNotesRepository
{
    /**
     * Get accumulated Aset Netto subcategory GL balances for a given year.
     *
     * Only queries COA accounts belonging to the "Aset Netto" account category
     * (financial_report_type_id = 1). Returns an associative array keyed by
     * subcategory name with the net balance as the value.
     *
     * This is used to read the "Akumulasi Aset Netto Tidak Terikat" and
     * "Akumulasi Aset Netto Terikat" closing balances from GL postings.
     *
     * @param  int   $year  The fiscal year to query (e.g., 2025).
     * @return array<string, float>  e.g., ['Akumulasi Aset Netto Tidak Terikat' => 1200000.0, ...]
     */
    public function getAsetNettoBalances(int $year): array
    {
        $startDate = "{$year}-01-01";
        $endDate   = "{$year}-12-31";

        $rows = DB::table('chart_of_accounts as coa')
            ->join('account_subcategories as sub', 'coa.account_subcategory_id', '=', 'sub.id')
            ->join('account_categories as cat', 'sub.account_category_id', '=', 'cat.id')
            ->leftJoin('general_ledgers as gl', 'gl.chart_of_account_id', '=', 'coa.id')
            ->leftJoin('transactions as t', function ($join) use ($startDate, $endDate) {
                $join->on('gl.transaction_id', '=', 't.id')
                     ->whereBetween('t.transaction_date', [$startDate, $endDate]);
            })
            ->where('coa.financial_report_type_id', 1)
            ->where('cat.name', 'Aset Netto')
            ->groupBy(
                'coa.id',
                'coa.normal_balance',
                'sub.name',
                'sub.id',
                'cat.name',
                'cat.id',
            )
            ->select([
                'sub.name as subcategory_name',
                DB::raw("
                    COALESCE(SUM(
                        CASE WHEN t.id IS NOT NULL THEN
                            CASE WHEN coa.normal_balance = 'C'
                                THEN gl.credit - gl.debit
                                ELSE gl.debit - gl.credit
                            END
                        ELSE 0 END
                    ), 0) as balance
                "),
            ])
            ->orderBy('sub.id')
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $key = $row->subcategory_name;
            if (!isset($result[$key])) {
                $result[$key] = 0.0;
            }
            $result[$key] += (float) $row->balance;
        }

        return $result;
    }

    /**
     * Get the current financial year from the organization profile.
     * Falls back to the current calendar year if not configured.
     */
    public function getCurrentYear(): int
    {
        try {
            $profile = app(OrganizationProfileService::class)->getProfile();
            if ($profile && $profile->financial_period_end) {
                return (int) $profile->financial_period_end->format('Y');
            }
        } catch (\Throwable $e) {
        }

        return (int) now()->format('Y');
    }
}
