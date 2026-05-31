<?php

namespace App\Services;

use App\Data\AdjustingEntryData;
use App\Enums\TransactionTypeEnum;
use App\Enums\JournalEntryTypeEnum;
use App\Models\Transaction;
use App\Models\Program;
use App\Models\User;
use App\Repositories\ChartOfAccountRepository;
use App\Repositories\GeneralLedgerRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdjustingEntryService
{
    public function __construct(
        protected TransactionRepository    $transactionRepository,
        protected GeneralLedgerRepository  $generalLedgerRepository,
        protected ChartOfAccountRepository $coaRepository,
        protected DocumentNumberService    $documentNumberService,
    ) {}

    public function getPageData(array $filters = []): array
    {
        $programsRaw = Cache::remember(
            'programs.all',
            now()->addMinutes(10),
            fn () => Program::all()->map(fn($p) => $p->getAttributes())->toArray()
        );

        $usersRaw = Cache::remember(
            'users.all',
            now()->addMinutes(10),
            fn () => User::all()->map(fn($u) => $u->getAttributes())->toArray()
        );

        return [
            'allAccounts'        => $this->coaRepository->getTransactionAccounts(),
            'programs'           => Program::hydrate($programsRaw),
            'users'              => User::hydrate($usersRaw),
            'nextDocumentNumber' => $this->documentNumberService->generate(TransactionTypeEnum::ADJUSTMENT),
            'adjustingEntries'   => $this->transactionRepository->getPaginated(TransactionTypeEnum::ADJUSTMENT, $filters),
        ];
    }

    public function store(AdjustingEntryData $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $documentNumber = $data->documentNumber
                ?: $this->documentNumberService->generate(TransactionTypeEnum::ADJUSTMENT);

            $description = $data->description;
            if ($data->journalEntryType === JournalEntryTypeEnum::BEGINNING_BALANCES) {
                // Prepend [SALDO AWAL] as approved
                $description = '[SALDO AWAL] ' . ($data->description ?? '');
            }

            $transaction = $this->transactionRepository->create([
                'transaction_date' => $data->transactionDate,
                'document_number'  => $documentNumber,
                'transaction_type' => TransactionTypeEnum::ADJUSTMENT->value,
                'program_id'       => $data->programId,
                'user_id'          => $data->userId,
                'reference'        => $data->reference,
                'description'      => $description,
            ]);

            // GL Double-Entry:
            // 1. Debit  → COA Transaksi (debitAccountId)
            // 2. Credit → Lawan COA Transaksi (creditAccountId)
            $this->generalLedgerRepository->createMany([
                [
                    'transaction_id'      => $transaction->id,
                    'chart_of_account_id' => $data->debitAccountId,
                    'debit'               => $data->amount,
                    'credit'              => 0,
                    'note'                => $data->journalEntryType->value,
                ],
                [
                    'transaction_id'      => $transaction->id,
                    'chart_of_account_id' => $data->creditAccountId,
                    'debit'               => 0,
                    'credit'              => $data->amount,
                    'note'                => $data->journalEntryType->value,
                ],
            ]);

            return $transaction;
        });
    }

    public function update(Transaction $transaction, AdjustingEntryData $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            // Check cross-feature editing cases
            $reference = $data->reference;
            if ($transaction->transaction_type === TransactionTypeEnum::INCOME) {
                $cashGl = $transaction->generalLedgers->first(fn($gl) => (float) $gl->debit > 0);
                if ($cashGl) {
                    $reference = $cashGl->chart_of_account_id;
                }
            } elseif ($transaction->transaction_type === TransactionTypeEnum::EXPENSE) {
                $cashGl = $transaction->generalLedgers->first(fn($gl) => (float) $gl->credit > 0);
                if ($cashGl) {
                    $reference = $cashGl->chart_of_account_id;
                }
            }

            $description = $data->description;
            if ($data->journalEntryType === JournalEntryTypeEnum::BEGINNING_BALANCES) {
                if ($description === null || !str_starts_with($description, '[SALDO AWAL]')) {
                    $description = '[SALDO AWAL] ' . ($description ?? '');
                }
            }

            $transaction->update([
                'transaction_date' => $data->transactionDate,
                'document_number'  => $data->documentNumber ?: $transaction->document_number,
                'transaction_type' => TransactionTypeEnum::ADJUSTMENT->value, // Force to ADJUSTMENT if cross-feature edit
                'program_id'       => $data->programId,
                'user_id'          => $data->userId,
                'reference'        => $reference,
                'description'      => $description,
            ]);

            $transaction->generalLedgers()->delete();

            $this->generalLedgerRepository->createMany([
                [
                    'transaction_id'      => $transaction->id,
                    'chart_of_account_id' => $data->debitAccountId,
                    'debit'               => $data->amount,
                    'credit'              => 0,
                    'note'                => $data->journalEntryType->value,
                ],
                [
                    'transaction_id'      => $transaction->id,
                    'chart_of_account_id' => $data->creditAccountId,
                    'debit'               => 0,
                    'credit'              => $data->amount,
                    'note'                => $data->journalEntryType->value,
                ],
            ]);

            return $transaction;
        });
    }

    public function destroy(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $transaction->generalLedgers()->delete();
            $transaction->delete();
        });
    }
}
