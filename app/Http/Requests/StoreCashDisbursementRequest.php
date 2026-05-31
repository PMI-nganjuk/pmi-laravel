<?php

namespace App\Http\Requests;

use App\Enums\TransactionTypeEnum;
use App\Rules\ActiveFinancialPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCashDisbursementRequest extends FormRequest
{
    /**
     * Inject transaction_type so it is never exposed to user input.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'transaction_type' => TransactionTypeEnum::EXPENSE->value,
        ]);
    }

    /**
     * Only users with the finance.create permission may store disbursements.
     */
    public function authorize(): bool
    {
        return $this->user()->can('finance.create');
    }

    public function rules(): array
    {
        $transactionId = $this->route('transaction')?->id;

        return [
            'transaction_date'         => [
                'required',
                'date',
                new ActiveFinancialPeriod()
            ],
            'cash_account_code'        => ['required', 'string', 'exists:chart_of_accounts,id'],
            'transaction_account_code' => ['required', 'string', 'exists:chart_of_accounts,id'],
            'amount'                   => ['required', 'numeric', 'min:1'],
            'description'              => ['nullable', 'string', 'max:500'],
            'reference'                => ['nullable', 'string', 'max:100'],
            'program_id'               => ['nullable', 'integer', 'exists:programs,id'],
            'user_id'                  => ['required', 'integer', 'exists:users,id'],
            'transaction_type'         => ['required', 'string', 'in:PENGELUARAN'],
            'document_number'          => [
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
            'transaction_date.required'         => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date'             => 'Format tanggal tidak valid.',
            'cash_account_code.required'        => 'Rekening kas wajib dipilih.',
            'cash_account_code.exists'          => 'Rekening kas yang dipilih tidak ditemukan.',
            'transaction_account_code.required' => 'Kode transaksi wajib dipilih.',
            'transaction_account_code.exists'   => 'Kode transaksi yang dipilih tidak ditemukan.',
            'amount.required'                   => 'Jumlah nominal wajib diisi.',
            'amount.numeric'                    => 'Jumlah nominal harus berupa angka.',
            'amount.min'                        => 'Jumlah nominal minimal Rp 1.',
            'program_id.exists'                 => 'Program yang dipilih tidak ditemukan.',
            'user_id.required'                  => 'Penerima (Dibayarkan Kepada) wajib dipilih.',
            'user_id.exists'                    => 'Penerima yang dipilih tidak ditemukan.',
            'document_number.unique'            => 'Nomor dokumen sudah digunakan.',
        ];
    }
}
