<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organization_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'chairperson' => ['nullable', 'string', 'max:255'],
            'headquarters_treasurer' => ['nullable', 'string', 'max:255'],
            'blood_donation_unit_treasurer' => ['nullable', 'string', 'max:255'],
            'financial_period_start' => ['nullable', 'date'],
            'financial_period_end' => ['nullable', 'date', 'after_or_equal:financial_period_start'],
            'fiscal_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'organization_name' => 'nama entitas',
            'address' => 'alamat',
            'chairperson' => 'ketua',
            'headquarters_treasurer' => 'bendahara markas',
            'blood_donation_unit_treasurer' => 'bendahara UDD',
            'financial_period_start' => 'periode awal',
            'financial_period_end' => 'periode akhir',
            'fiscal_year' => 'tahun buku',
        ];
    }
}
