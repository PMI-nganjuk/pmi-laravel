<?php

namespace App\Services;

use App\Data\CashDisbursementData;
use App\Enums\TransactionTypeEnum;
use App\Models\ChartOfAccount;
use App\Models\Transaction;
use App\Models\Program;
use App\Models\User;
use App\Repositories\ChartOfAccountRepository;
use App\Repositories\GeneralLedgerRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CashDisbursementService
{
    public function __construct(
        protected TransactionRepository    $transactionRepository,
        protected GeneralLedgerRepository  $generalLedgerRepository,
        protected ChartOfAccountRepository $coaRepository,
        protected DocumentNumberService    $documentNumberService,
    ) {}

    public function getPageData(array $filters = []): array
    {
        return [
            'cashAccounts'        => $this->coaRepository->getCashAccounts(),
            'transactionAccounts' => $this->coaRepository->getTransactionAccounts(),
            'programs'            => Cache::remember('programs.all', now()->addMinutes(10), fn () => Program::all()),
            'users'               => Cache::remember('users.all', now()->addMinutes(10), fn () => User::all()),
            'nextDocumentNumber'  => $this->documentNumberService->generate(TransactionTypeEnum::EXPENSE),
            'disbursements'       => $this->transactionRepository->getPaginated(TransactionTypeEnum::EXPENSE, $filters),
        ];
    }

    public function store(CashDisbursementData $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $documentNumber = $data->documentNumber
                ?: $this->documentNumberService->generate(TransactionTypeEnum::EXPENSE);

            $transaction = $this->transactionRepository->create([
                'transaction_date' => $data->transactionDate,
                'document_number'  => $documentNumber,
                'transaction_type' => TransactionTypeEnum::EXPENSE->value,
                'program_id'       => $data->programId,
                'user_id'          => $data->userId,
                'reference'        => $data->reference,
                'description'      => $data->description,
            ]);

            // GL Double-Entry (inverted from receipt):
            // Debit  → Kode Transaksi (beban/hutang yang bertambah)
            // Credit → Rekening Kas   (kas/bank yang berkurang)
            $this->generalLedgerRepository->createMany([
                [
                    'transaction_id'      => $transaction->id,
                    'chart_of_account_id' => $data->transactionAccountCode,
                    'debit'               => $data->amount,
                    'credit'              => 0,
                    'note'                => 'Kode transaksi pengeluaran',
                ],
                [
                    'transaction_id'      => $transaction->id,
                    'chart_of_account_id' => $data->cashAccountCode,
                    'debit'               => 0,
                    'credit'              => $data->amount,
                    'note'                => 'Pengeluaran kas',
                ],
            ]);

            return $transaction;
        });
    }

    public function update(Transaction $transaction, CashDisbursementData $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            $transaction->update([
                'transaction_date' => $data->transactionDate,
                'document_number'  => $data->documentNumber ?: $transaction->document_number,
                'program_id'       => $data->programId,
                'user_id'          => $data->userId,
                'reference'        => $data->reference,
                'description'      => $data->description,
            ]);

            $transaction->generalLedgers()->delete();

            $this->generalLedgerRepository->createMany([
                [
                    'transaction_id'      => $transaction->id,
                    'chart_of_account_id' => $data->transactionAccountCode,
                    'debit'               => $data->amount,
                    'credit'              => 0,
                    'note'                => 'Kode transaksi pengeluaran',
                ],
                [
                    'transaction_id'      => $transaction->id,
                    'chart_of_account_id' => $data->cashAccountCode,
                    'debit'               => 0,
                    'credit'              => $data->amount,
                    'note'                => 'Pengeluaran kas',
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

    /**
     * Suggest a description based on keyword matching in the transaction account name.
     *
     * Rules (keyword → suggestion):
     * - "Hutang"                → "Pembayaran Hutang {NamaAkun}"
     * - "Tunjangan"             → "Pembayaran Manajemen Organisasi {Bulan} {Tahun}"
     * - "BPJS"                  → "Pembayaran BPJS {Bulan} {Tahun}"
     * - "Gaji"                  → "Pembayaran Gaji {Bulan} {Tahun}"
     * - "Insentif"              → "Pembayaran Jasa {Bulan} {Tahun}"
     * - "Internet, Listrik dan Air" → "Pembayaran Rekening {Bulan} {Tahun}"
     */
    public function suggestDescription(string $transactionAccountCode): ?string
    {
        $coa = ChartOfAccount::find($transactionAccountCode);

        if ($coa === null) {
            return null;
        }

        $accountName = $coa->account_name;
        $month       = now()->translatedFormat('F');
        $year        = now()->year;

        return match (true) {
            str_contains($accountName, 'Internet, Listrik dan Air')
                => "Pembayaran Rekening {$month} {$year}",

            str_contains($accountName, 'Hutang')
                => "Pembayaran Hutang {$accountName}",

            str_contains($accountName, 'Tunjangan')
                => "Pembayaran Manajemen Organisasi {$month} {$year}",

            str_contains($accountName, 'BPJS')
                => "Pembayaran BPJS {$month} {$year}",

            str_contains($accountName, 'Gaji')
                => "Pembayaran Gaji {$month} {$year}",

            str_contains($accountName, 'Insentif')
                => "Pembayaran Jasa {$month} {$year}",

            default => '',
        };
    }
}
