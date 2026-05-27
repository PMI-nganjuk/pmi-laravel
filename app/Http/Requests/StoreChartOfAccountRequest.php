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
            'account_category_id'   => 'required|integer|exists:account_categories,id',
            'account_subcategory_id'   => 'required|integer|exists:account_subcategories,id',
            'id'             => [
                'required',
                'string',
                'max:15',
                Rule::unique('chart_of_accounts', 'id')->ignore($currentId, 'id'),
            ],
            'account_name'   => 'required|string|max:100',
            'normal_balance'     => 'required|string',
            'financial_report_type_id' => 'required|exists:financial_report_types,id',
        ];
    }
}
