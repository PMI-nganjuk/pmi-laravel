<?php

namespace App\Data;

use Carbon\Carbon;

class CashReceiptData
{
    public function __construct(
        public readonly string  $transactionDate,
        public readonly string  $cashAccountCode,
        public readonly string  $transactionAccountCode,
        public readonly float   $amount,
        public readonly int     $userId,
        public readonly ?string $description,
        public readonly ?string $reference,
        public readonly ?int    $programId,
        public readonly ?string $documentNumber = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            transactionDate:        $data['transaction_date'],
            cashAccountCode:        $data['cash_account_code'],
            transactionAccountCode: $data['transaction_account_code'],
            amount:                 (float) $data['amount'],
            userId:                 (int) $data['user_id'],
            description:            $data['description'] ?? null,
            reference:              $data['reference'] ?? null,
            programId:              isset($data['program_id']) ? (int) $data['program_id'] : null,
            documentNumber:         $data['document_number'] ?? null,
        );
    }
}
