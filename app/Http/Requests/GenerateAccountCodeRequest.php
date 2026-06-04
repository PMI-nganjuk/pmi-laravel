<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAccountCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_category_id' => ['required', 'integer', 'exists:account_categories,id'],
            'account_subcategory_id' => ['required', 'integer', 'exists:account_subcategories,id'],
        ];
    }
}
