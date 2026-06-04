<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfitLossReportRequest extends FormRequest
{
    /**
     * Memeriksa otorisasi pengguna untuk melihat laporan keuangan.
     */
    public function authorize(): bool
    {
        return $this->user()->can('finance.view');
    }

    /**
     * Mengembalikan aturan validasi untuk input tanggal periode laporan.
     */
    public function rules(): array
    {
        return [
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date'   => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ];
    }
}
