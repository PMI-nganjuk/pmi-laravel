<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetCategoryTwoOptionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_one' => ['required', 'string'],
        ];
    }
}
