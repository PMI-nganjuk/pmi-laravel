<?php

namespace App\Repositories;

use App\Models\GeneralLedger;
use App\Services\OrganizationProfileService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProfitLossRepository
{
    /**
     * Get aggregated PL balances for each COA account within a date range.
     */
    public function getAccountBalances(string $startDate, string $endDate): Collection
    {
        return DB::table('chart_of_accounts as coa')
            ->join('account_subcategories as sub', 'coa.account_subcategory_id', '=', 'sub.id')
            ->join('account_categories as cat', 'sub.account_category_id', '=', 'cat.id')
            ->leftJoin('general_ledgers as gl', 'gl.chart_of_account_id', '=', 'coa.id')
            ->leftJoin('transactions as t', function ($join) use ($startDate, $endDate) {
                $join->on('gl.transaction_id', '=', 't.id')
                     ->whereBetween('t.transaction_date', [$startDate, $endDate]);
            })
            ->where('coa.financial_report_type_id', 2)
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
                    CASE WHEN coa.normal_balance = 'C'
                        THEN SUM(CASE WHEN t.id IS NOT NULL AND t.program_id IS NULL THEN gl.credit - gl.debit ELSE 0 END)
                        ELSE SUM(CASE WHEN t.id IS NOT NULL AND t.program_id IS NULL THEN gl.debit - gl.credit ELSE 0 END)
                    END as tidak_terikat
                "),

                DB::raw("
                    CASE WHEN coa.normal_balance = 'C'
                        THEN SUM(CASE WHEN t.id IS NOT NULL AND t.program_id IS NOT NULL THEN gl.credit - gl.debit ELSE 0 END)
                        ELSE SUM(CASE WHEN t.id IS NOT NULL AND t.program_id IS NOT NULL THEN gl.debit - gl.credit ELSE 0 END)
                    END as terikat
                "),

                DB::raw("
                    CASE WHEN coa.normal_balance = 'C'
                        THEN SUM(CASE WHEN t.id IS NOT NULL THEN gl.credit - gl.debit ELSE 0 END)
                        ELSE SUM(CASE WHEN t.id IS NOT NULL THEN gl.debit - gl.credit ELSE 0 END)
                    END as total
                "),
            ])
            ->orderBy('coa.id')
            ->get();
    }

    /**
     * Get the organization's configured financial period.
     */
    public function getFinancialPeriod(): array
    {
        try {
            $profile = app(OrganizationProfileService::class)->getProfile();
            if ($profile && $profile->financial_period_start && $profile->financial_period_end) {
                return [
                    'start' => $profile->financial_period_start->format('Y-m-d'),
                    'end'   => $profile->financial_period_end->format('Y-m-d'),
                ];
            }
        } catch (\Throwable $e) {
        }

        return [
            'start' => now()->startOfYear()->format('Y-m-d'),
            'end'   => now()->endOfYear()->format('Y-m-d'),
        ];
    }
}
