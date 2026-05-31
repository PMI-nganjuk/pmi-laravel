<?php

namespace App\Data;

use App\Enums\JournalEntryTypeEnum;

class AdjustingEntryData
{
    public function __construct(
        public readonly string  $transactionDate,
        public readonly string  $debitAccountId,
        public readonly string  $creditAccountId,
        public readonly float   $amount,
        public readonly int     $userId,
        public readonly JournalEntryTypeEnum $journalEntryType,
        public readonly ?string $description = null,
        public readonly ?string $reference = null,
        public readonly ?int    $programId = null,
        public readonly ?string $documentNumber = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            transactionDate:  $data['transaction_date'],
            debitAccountId:   $data['debit_account_id'],
            creditAccountId:  $data['credit_account_id'],
            amount:           (float) $data['amount'],
            userId:           (int) $data['user_id'],
            journalEntryType: $data['journal_entry_type'] instanceof JournalEntryTypeEnum
                ? $data['journal_entry_type']
                : JournalEntryTypeEnum::from($data['journal_entry_type']),
            description:      $data['description'] ?? null,
            reference:        $data['reference'] ?? null,
            programId:        isset($data['program_id']) ? (int) $data['program_id'] : null,
            documentNumber:   $data['document_number'] ?? null,
        );
    }
}
