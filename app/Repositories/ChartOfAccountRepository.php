<?php

namespace App\Repositories;

use App\Models\ChartOfAccount;
use App\Models\AccountCategory;
use App\Models\AccountSubcategory;
use App\Models\FinancialReportType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;

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
        return ChartOfAccount::create($attributes);
    }

    public function update(ChartOfAccount $chartOfAccount, array $attributes): ChartOfAccount
    {
        $chartOfAccount->update($attributes);

        return $chartOfAccount;
    }

    public function delete(ChartOfAccount $chartOfAccount): void
    {
        $chartOfAccount->delete();
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
}
