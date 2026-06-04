<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountSubcategory extends Model
{
    use HasFactory;

    protected $table = 'account_subcategories';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'name',
        'account_category_id',
    ];

    public function accountCategory()
    {
        return $this->belongsTo(AccountCategory::class, 'account_category_id', 'id');
    }
}
