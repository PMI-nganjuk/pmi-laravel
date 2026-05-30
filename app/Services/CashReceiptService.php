<?php

namespace App\Services;

use App\Data\CashReceiptData;
use App\Enums\TransactionTypeEnum;
use App\Models\ChartOfAccount;
use App\Models\Transaction;
use App\Models\Program;
use App\Models\User;
use App\Repositories\ChartOfAccountRepository;
use App\Repositories\GeneralLedgerRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\DB;

class CashReceiptService
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
            'programs'            => Program::all(),
            'users'               => User::all(),
            'nextDocumentNumber'  => $this->documentNumberService->generate(TransactionTypeEnum::INCOME),
            'receipts'            => $this->transactionRepository->getPaginated($filters),
        ];
    }

    public function store(CashReceiptData $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $documentNumber = $data->documentNumber ?: $this->documentNumberService->generate(TransactionTypeEnum::INCOME);

            $transaction = $this->transactionRepository->create([
                'transaction_date' => $data->transactionDate,
                'document_number'  => $documentNumber,
                'transaction_type' => TransactionTypeEnum::INCOME->value,
                'program_id'       => $data->programId,
                'user_id'          => $data->userId,
                'reference'        => $data->reference,
                'description'      => $data->description,
            ]);

            $this->generalLedgerRepository->createMany([
                [
                    'transaction_id'      => $transaction->id,
                    'chart_of_account_id' => $data->cashAccountCode,
                    'debit'               => $data->amount,
                    'credit'              => 0,
                    'note'                => 'Penerimaan kas',
                ],
                [
                    'transaction_id'      => $transaction->id,
                    'chart_of_account_id' => $data->transactionAccountCode,
                    'debit'               => 0,
                    'credit'              => $data->amount,
                    'note'                => 'Kode transaksi',
                ],
            ]);

            return $transaction;
        });
    }

    public function suggestDescription(string $transactionAccountCode): ?string
    {
        $coa = ChartOfAccount::with('accountSubcategory.accountCategory')
            ->find($transactionAccountCode);

        if ($coa === null) {
            return null;
        }

        $accountName   = $coa->account_name;
        $subcategoryId = $coa->accountSubcategory?->id;
        $categoryId    = $coa->accountSubcategory?->accountCategory?->id;

        $month = now()->translatedFormat('F');
        $year  = now()->year;

        return match (true) {
            $accountName === 'Penghasilan Lainnya'
                => "Penerimaan Bunga Bank {$month} {$year}",

            $subcategoryId === 2 && $categoryId === 1
                => 'Penarikan Uang Tunai Dari Bank ',

            $accountName === 'Pendapatan Netto Tidak Terikat Periode Berjalan'
                => 'Penerimaan BPPD Kuitansi No. ',

            $subcategoryId === 3 && $categoryId === 1
                => "Pembayaran Piutang {$accountName} {$month} {$year}",

            default => '',
        };
    }

    public function update(Transaction $transaction, CashReceiptData $data): Transaction
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
                    'chart_of_account_id' => $data->cashAccountCode,
                    'debit'               => $data->amount,
                    'credit'              => 0,
                    'note'                => 'Penerimaan kas',
                ],
                [
                    'transaction_id'      => $transaction->id,
                    'chart_of_account_id' => $data->transactionAccountCode,
                    'debit'               => 0,
                    'credit'              => $data->amount,
                    'note'                => 'Kode transaksi',
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
