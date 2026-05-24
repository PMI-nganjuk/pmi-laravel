<?php

namespace App\Repositories;

use App\Models\ChartOfAccount;
use App\Models\CategoryOne;
use App\Models\CategoryTwo;
use App\Models\ReportTypes;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;

class ChartOfAccountRepository
{
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = ChartOfAccount::with(['categoryTwo.categoryOne', 'reportType']);

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhereHas('categoryTwo', function ($query) use ($search) {
                        $query->where('category_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('categoryTwo.categoryOne', function ($query) use ($search) {
                        $query->where('category_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('reportType', function ($query) use ($search) {
                        $query->where('report_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($entryType = $filters['entry_type'] ?? null) {
            $query->where('entry_type', $entryType);
        }

        if ($reportTypeId = $filters['report_type_id'] ?? null) {
            $query->where('report_type_id', $reportTypeId);
        }

        $allowedSorts = ['id', 'account_name', 'entry_type', 'report_type_id', 'created_at'];
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

    public function getCategoryOneOptions(): SupportCollection
    {
        return CategoryOne::pluck('category_name', 'category_code');
    }

    public function getCategoryTwoOptions(string $categoryOneCode): SupportCollection
    {
        return CategoryTwo::where('category_one', $categoryOneCode)
            ->pluck('category_name', 'category_code');
    }

    public function getReportTypeOptions(): SupportCollection
    {
        return ReportTypes::pluck('report_name', 'id');
    }

    public function getLastAccountCodeByPrefix(string $prefix): ?string
    {
        return ChartOfAccount::where('id', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('id');
    }
}
