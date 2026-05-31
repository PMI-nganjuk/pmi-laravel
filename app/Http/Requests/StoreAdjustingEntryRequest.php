<?php

namespace App\Http\Requests;

use App\Enums\TransactionTypeEnum;
use App\Enums\JournalEntryTypeEnum;
use App\Rules\ActiveFinancialPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdjustingEntryRequest extends FormRequest
{
    /**
     * Inject transaction_type so it is never exposed to user input.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'transaction_type' => TransactionTypeEnum::ADJUSTMENT->value,
        ]);
    }

    /**
     * Only users with the finance.create permission may store adjusting entries.
     */
    public function authorize(): bool
    {
        return $this->user()->can('finance.create');
    }

    public function rules(): array
    {
        $transactionId = $this->route('transaction')?->id;

        return [
            'transaction_date'  => [
                'required',
                'date',
                new ActiveFinancialPeriod(),
            ],
            'debit_account_id'  => ['required', 'string', 'exists:chart_of_accounts,id'],
            'credit_account_id' => [
                'required',
                'string',
                'exists:chart_of_accounts,id',
                'different:debit_account_id',
            ],
            'amount'            => ['required', 'numeric', 'min:1'],
            'description'       => ['nullable', 'string', 'max:500'],
            'reference'         => ['nullable', 'string', 'max:100'],
            'program_id'        => ['nullable', 'integer', 'exists:programs,id'],
            'user_id'           => ['required', 'integer', 'exists:users,id'],
            'journal_entry_type'=> ['required', 'string', Rule::in([
                JournalEntryTypeEnum::BEGINNING_BALANCES->value,
                JournalEntryTypeEnum::ADJUSTING_ENTRIES->value,
            ])],
            'transaction_type'  => ['required', 'string', Rule::in([TransactionTypeEnum::ADJUSTMENT->value])],
            'document_number'   => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('transactions', 'document_number')->ignore($transactionId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date'     => 'Format tanggal tidak valid.',
            'debit_account_id.required' => 'COA Transaksi (Debit) wajib dipilih.',
            'debit_account_id.exists'   => 'COA Transaksi yang dipilih tidak ditemukan.',
            'credit_account_id.required'=> 'Lawan COA Transaksi (Kredit) wajib dipilih.',
            'credit_account_id.exists'  => 'Lawan COA Transaksi yang dipilih tidak ditemukan.',
            'credit_account_id.different'=> 'Lawan COA Transaksi tidak boleh sama dengan COA Transaksi.',
            'amount.required'           => 'Jumlah nominal wajib diisi.',
            'amount.numeric'            => 'Jumlah nominal harus berupa angka.',
            'amount.min'                => 'Jumlah nominal minimal Rp 1.',
            'program_id.exists'         => 'Program yang dipilih tidak ditemukan.',
            'user_id.required'          => 'Penerima/User wajib dipilih.',
            'user_id.exists'            => 'Penerima yang dipilih tidak ditemukan.',
            'journal_entry_type.required'=> 'Entri jurnal wajib dipilih.',
            'journal_entry_type.in'     => 'Entri jurnal tidak valid.',
            'document_number.unique'    => 'Nomor dokumen sudah digunakan.',
        ];
    }
}
