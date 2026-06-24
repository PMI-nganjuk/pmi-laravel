<?php

namespace App\Repositories;

use App\Services\OrganizationProfileService;
use Illuminate\Support\Facades\DB;

class CashFlowRepository
{
    /**
     * Get the net GL balance for one or more specific COA account IDs within a date range.
     *
     * For Credit-normal accounts: returns SUM(credit - debit).
     * For Debit-normal accounts:  returns SUM(debit - credit).
     * This gives the "effective positive balance" of the account for the period.
     *
     * @param  string[] $coaIds     Array of COA IDs, e.g., ['52001-00', '52011-00']
     * @param  string   $startDate  Format: Y-m-d
     * @param  string   $endDate    Format: Y-m-d
     * @return float
     */
    public function getGLNetBalanceByCoaIds(array $coaIds, string $startDate, string $endDate): float
    {
        $result = DB::table('general_ledgers as gl')
            ->join('transactions as t', 'gl.transaction_id', '=', 't.id')
            ->join('chart_of_accounts as coa', 'gl.chart_of_account_id', '=', 'coa.id')
            ->whereIn('gl.chart_of_account_id', $coaIds)
            ->whereBetween('t.transaction_date', [$startDate, $endDate])
            ->selectRaw("
                COALESCE(SUM(
                    CASE WHEN coa.normal_balance = 'C'
                        THEN gl.credit - gl.debit
                        ELSE gl.debit - gl.credit
                    END
                ), 0) as net_balance
            ")
            ->value('net_balance');

        return (float) ($result ?? 0.0);
    }

    /**
     * Get all Balance Sheet account subcategory balances for a given year,
     * indexed by subcategory name.
     *
     * Uses the same query logic as BalanceSheetRepository::getAccountBalances()
     * but returns the result indexed as an associative array for direct lookup.
     * Only queries accounts with financial_report_type_id = 1 (Neraca/BS).
     *
     * @param  int  $year
     * @return array<string, float>  e.g., ['Kas' => 50000000.0, 'Bank' => 200000000.0, ...]
     */
    public function getBSSubcategoryBalances(int $year): array
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
            ->groupBy(
                'coa.id',
                'coa.normal_balance',
                'sub.name',
                'sub.id',
                'cat.id',
            )
            ->select([
                'sub.name as subcategory_name',
                DB::raw("
                    COALESCE(SUM(
                        CASE WHEN t.id IS NOT NULL THEN
                            CASE WHEN coa.normal_balance = 'D'
                                THEN gl.debit - gl.credit
                                ELSE gl.credit - gl.debit
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
