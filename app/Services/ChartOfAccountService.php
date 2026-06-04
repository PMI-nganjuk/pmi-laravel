<?php

namespace App\Services;

use App\Data\ChartOfAccountData;
use App\Enums\EntryTypeEnum;
use App\Models\ChartOfAccount;
use App\Models\AccountSubcategory;
use App\Repositories\ChartOfAccountRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ChartOfAccountService
{
    public function __construct(
        protected ChartOfAccountRepository $repository
    ) {}

    public function getPageData(array $filters = []): array
    {
        return [
            'accountCategoryOptions' => $this->repository->getAccountCategoryOptions(),
            'financialReportTypeOptions' => $this->repository->getFinancialReportTypeOptions(),
            'normalBalanceOptions' => EntryTypeEnum::cases(),
            'coas' => $this->repository->getPaginated($filters),
        ];
    }

    public function getAccountSubcategoryOptions(int $accountCategoryId): Collection
    {
        return $this->repository->getAccountSubcategoryOptions($accountCategoryId);
    }

    public function generateAccountCode(?int $accountCategoryId, ?int $accountSubcategoryId): ?string
    {
        if (!$accountCategoryId || !$accountSubcategoryId) {
            return null;
        }

        $subCategoryIndex = AccountSubcategory::where('account_category_id', $accountCategoryId)
            ->where('id', '<=', $accountSubcategoryId)
            ->count();

        $prefix = $accountCategoryId . $subCategoryIndex;
        $lastAccountCode = $this->repository->getLastAccountCodeByPrefix($prefix . '%');

        $sequence = 0;
        if ($lastAccountCode) {
            $sequence = (int) substr($lastAccountCode, 2, 2);
            $sequence++;
        }

        $formattedSequence = str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);

        return sprintf('%s%s1-00', $prefix, $formattedSequence);
    }

    public function store(ChartOfAccountData $data): ChartOfAccount
    {
        return DB::transaction(function () use ($data) {
            return $this->repository->create($data->toAttributes());
        });
    }

    public function update(ChartOfAccount $chartOfAccount, ChartOfAccountData $data): ChartOfAccount
    {
        return DB::transaction(function () use ($chartOfAccount, $data) {
            return $this->repository->update($chartOfAccount, $data->toAttributes());
        });
    }

    public function delete(ChartOfAccount $chartOfAccount): void
    {
        DB::transaction(function () use ($chartOfAccount) {
            $this->repository->delete($chartOfAccount);
        });
    }
}
