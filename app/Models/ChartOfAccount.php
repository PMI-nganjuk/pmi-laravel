<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $table = 'chart_of_accounts';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'account_subcategory_id',
        'account_name',
        'normal_balance',
        'financial_report_type_id',
    ];

    public function accountSubcategory()
    {
        return $this->belongsTo(AccountSubcategory::class, 'account_subcategory_id', 'id');
    }

    public function financialReportType()
    {
        return $this->belongsTo(FinancialReportType::class, 'financial_report_type_id', 'id');
    }
}
