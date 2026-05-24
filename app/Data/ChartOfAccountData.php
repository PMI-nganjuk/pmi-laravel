<?php

namespace App\Data;

class ChartOfAccountData
{
    public function __construct(
        public readonly string $accountCode,
        public readonly string $categoryTwo,
        public readonly string $accountName,
        public readonly string $entryType,
        public readonly int $reportTypeId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            accountCode: $data['id'],
            categoryTwo: $data['category_two'],
            accountName: $data['account_name'],
            entryType: $data['entry_type'],
            reportTypeId: (int) $data['report_type_id'],
        );
    }

    public function toAttributes(): array
    {
        return [
            'id' => $this->accountCode,
            'category_two' => $this->categoryTwo,
            'account_name' => $this->accountName,
            'entry_type' => $this->entryType,
            'report_type_id' => $this->reportTypeId,
        ];
    }
}
