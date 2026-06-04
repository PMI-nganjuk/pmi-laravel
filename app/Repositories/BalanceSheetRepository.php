<?php

namespace App\Repositories;

use App\Services\OrganizationProfileService;
use Illuminate\Support\Facades\DB;

class BalanceSheetRepository
{
    /**
     * Get aggregated balance sheet balances per COA subcategory for a given year.
     * Accumulates all GL entries from the beginning of the year up to year-end.
     */
    public function getAccountBalances(int $year): \Illuminate\Support\Collection
    {
        $startDate = "{$year}-01-01";
        $endDate   = "{$year}-12-31";

        return DB::table('chart_of_accounts as coa')
            ->join('account_subcategories as sub', 'coa.account_subcategory_id', '=', 'sub.id')
            ->join('account_categories as cat', 'sub.account_category_id', '=', 'cat.id')
            ->leftJoin('general_ledgers as gl', 'gl.chart_of_account_id', '=', 'coa.id')
            ->leftJoin('transactions as t', function ($join) use ($startDate, $endDate) {
                $join->on('gl.transaction_id', '=', 't.id')
                     ->whereBetween('t.transaction_date', [$startDate, $endDate]);
            })
            ->where('coa.financial_report_type_id', 1) // 1 = Balance Sheet
            ->groupBy(
                'coa.id',
                'coa.account_name',
                'coa.normal_balance',
                'sub.name',
                'sub.id',
                'cat.name',
                'cat.id'
            )
            ->select([
                'coa.id as chart_of_account_id',
                'coa.account_name',
                'coa.normal_balance',
                'sub.name as subcategory_name',
                'sub.id as subcategory_id',
                'cat.name as category_name',
                'cat.id as category_id',

                DB::raw("
                    CASE WHEN coa.normal_balance = 'D'
                        THEN COALESCE(SUM(CASE WHEN t.id IS NOT NULL THEN gl.debit - gl.credit ELSE 0 END), 0)
                        ELSE COALESCE(SUM(CASE WHEN t.id IS NOT NULL THEN gl.credit - gl.debit ELSE 0 END), 0)
                    END as balance
                "),
            ])
            ->orderBy('cat.id')
            ->orderBy('sub.id')
            ->orderBy('coa.id')
            ->get();
    }

    /**
     * Get the current financial year from organization profile, fallback to current year.
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