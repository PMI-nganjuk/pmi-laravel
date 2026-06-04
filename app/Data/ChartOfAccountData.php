<?php

namespace App\Data;

class ChartOfAccountData
{
    public function __construct(
        public readonly string $accountCode,
        public readonly int $accountSubcategoryId,
        public readonly string $accountName,
        public readonly string $normalBalance,
        public readonly int $financialReportTypeId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            accountCode: $data['id'],
            accountSubcategoryId: (int) $data['account_subcategory_id'],
            accountName: $data['account_name'],
            normalBalance: $data['normal_balance'],
            financialReportTypeId: (int) $data['financial_report_type_id'],
        );
    }

    public function toAttributes(): array
    {
        return [
            'id' => $this->accountCode,
            'account_subcategory_id' => $this->accountSubcategoryId,
            'account_name' => $this->accountName,
            'normal_balance' => $this->normalBalance,
            'financial_report_type_id' => $this->financialReportTypeId,
        ];
    }
}
