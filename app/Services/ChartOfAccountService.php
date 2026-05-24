<?php

namespace App\Services;

use App\Data\ChartOfAccountData;
use App\Enums\EntryTypeEnum;
use App\Models\ChartOfAccount;
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
            'categoryOneOptions' => $this->repository->getCategoryOneOptions(),
            'reportTypeOptions' => $this->repository->getReportTypeOptions(),
            'entryTypeOptions' => EntryTypeEnum::cases(),
            'coas' => $this->repository->getPaginated($filters),
        ];
    }

    public function getCategoryTwoOptions(string $categoryOneCode): Collection
    {
        return $this->repository->getCategoryTwoOptions($categoryOneCode);
    }

    public function generateAccountCode(?string $categoryOneCode, ?string $categoryTwoCode): ?string
    {
        if (!$categoryOneCode || !$categoryTwoCode) {
            return null;
        }

        $prefix = $categoryOneCode . $categoryTwoCode;
        $lastAccountCode = $this->repository->getLastAccountCodeByPrefix($prefix);

        if ($lastAccountCode) {
            $mainPart = explode(' - ', $lastAccountCode)[0] ?? '';
            $sequence = (int) substr($mainPart, strlen($prefix));
            $nextSequence = str_pad($sequence + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextSequence = '001';
        }

        return $prefix . $nextSequence . ' - 00';
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
