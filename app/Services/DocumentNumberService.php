<?php

namespace App\Services;

use App\Enums\TransactionTypeEnum;
use App\Repositories\TransactionRepository;

class DocumentNumberService
{
    public function __construct(
        protected TransactionRepository $transactionRepository,
    ) {}

    public function generate(TransactionTypeEnum $type): string
    {
        $prefix = $type->documentPrefix();

        $lastDocumentNumber = $this->transactionRepository->getLastDocumentNumberByPrefix($prefix);

        if ($lastDocumentNumber === null) {
            $nextSequence = 1;
        } else {
            $sequencePart = (int) substr($lastDocumentNumber, strlen($prefix));
            $nextSequence = $sequencePart + 1;
        }

        return $prefix . str_pad((string) $nextSequence, 3, '0', STR_PAD_LEFT);
    }
}
