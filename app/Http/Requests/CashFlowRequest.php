<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashFlowRequest extends FormRequest
{
    /**
     * Memeriksa otorisasi pengguna untuk melihat laporan arus kas.
     */
    public function authorize(): bool
    {
        return $this->user()->can('finance.view');
    }

    /**
     * Mengembalikan aturan validasi untuk input tahun laporan.
     */
    public function rules(): array
    {
        return [
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ];
    }
}
