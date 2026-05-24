<?php

namespace App\Http\Requests;

use App\Models\ChartOfAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChartOfAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currentAccount = $this->route('coa');
        $currentId = $currentAccount instanceof ChartOfAccount
            ? $currentAccount->getKey()
            : $currentAccount;

        return [
            'category_one'   => 'required|string',
            'category_two'   => 'required|string',
            'id'             => [
                'required',
                'string',
                'max:15',
                Rule::unique('chart_of_accounts', 'id')->ignore($currentId, 'id'),
            ],
            'account_name'   => 'required|string|max:100',
            'entry_type'     => 'required|string',
            'report_type_id' => 'required|exists:report_types,id',
        ];
    }
}
