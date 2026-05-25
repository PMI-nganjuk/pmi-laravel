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
        'category_two',
        'account_name',
        'entry_type',
        'report_type_id',
    ];

    public function categoryOne()
    {
        return $this->belongsTo(CategoryOne::class, 'category_one', 'category_code');
    }

    public function categoryTwo()
    {
        return $this->belongsTo(CategoryTwo::class, 'category_two', 'category_code');
    }

    public function reportType()
    {
        return $this->belongsTo(ReportTypes::class, 'report_type_id', 'id');
    }
}
