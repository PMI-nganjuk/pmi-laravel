<?php

namespace App\Repositories;

use App\Models\ChartOfAccount;
use App\Models\AccountCategory;
use App\Models\AccountSubcategory;
use App\Models\FinancialReportType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;

class ChartOfAccountRepository
{
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = ChartOfAccount::with(['accountSubcategory.accountCategory', 'financialReportType']);

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhereHas('accountSubcategory', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('accountSubcategory.accountCategory', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('financialReportType', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($normalBalance = $filters['normal_balance'] ?? null) {
            $query->where('normal_balance', $normalBalance);
        }

        if ($reportTypeId = $filters['financial_report_type_id'] ?? null) {
            $query->where('financial_report_type_id', $reportTypeId);
        }

        $allowedSorts = ['id', 'account_name', 'normal_balance', 'financial_report_type_id', 'created_at'];
        $sortBy = in_array($filters['sort_by'] ?? null, $allowedSorts, true) ? $filters['sort_by'] : 'id';
        $sortDir = ($filters['sort_dir'] ?? null) === 'desc' ? 'desc' : 'asc';

        return $query
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $attributes): ChartOfAccount
    {
        $coa = ChartOfAccount::create($attributes);
        $this->clearCache();
        return $coa;
    }

    public function update(ChartOfAccount $chartOfAccount, array $attributes): ChartOfAccount
    {
        $chartOfAccount->update($attributes);
        $this->clearCache();
        return $chartOfAccount;
    }

    public function delete(ChartOfAccount $chartOfAccount): void
    {
        $chartOfAccount->delete();
        $this->clearCache();
    }

    protected function clearCache(): void
    {
        Cache::store('array')->forget('coa.cash_accounts');
        Cache::store('array')->forget('coa.transaction_accounts');
        Cache::forget('coa.cash_accounts');
        Cache::forget('coa.transaction_accounts');
    }

    public function getAccountCategoryOptions(): SupportCollection
    {
        return AccountCategory::pluck('name', 'id');
    }

    public function getAccountSubcategoryOptions(int $accountCategoryId): SupportCollection
    {
        return AccountSubcategory::where('account_category_id', $accountCategoryId)
            ->pluck('name', 'id');
    }

    public function getFinancialReportTypeOptions(): SupportCollection
    {
        return FinancialReportType::pluck('name', 'id');
    }

    public function getLastAccountCodeByPrefix(string $prefix): ?string
    {
        return ChartOfAccount::where('id', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('id');
    }

    public function getCashAccounts(): SupportCollection
    {
        return Cache::store('array')->remember(
            'coa.cash_accounts',
            now()->addHour(),
            fn () => ChartOfAccount::with('accountSubcategory')
                ->whereHas('accountSubcategory', function ($q) {
                    $q->where('id', '<=', 2)
                      ->where('account_category_id', 1);
                })
                ->orderBy('id')
                ->get(['id', 'account_name'])
        );
    }

    public function getTransactionAccounts(): SupportCollection
    {
        return Cache::store('array')->remember(
            'coa.transaction_accounts',
            now()->addHour(),
            fn () => ChartOfAccount::orderBy('id')
                ->get(['id', 'account_name'])
        );
    }
}
