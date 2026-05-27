<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountCategory extends Model
{
    use HasFactory;

    protected $table = 'account_categories';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'name',
    ];

    public function accountSubcategories()
    {
        return $this->hasMany(AccountSubcategory::class, 'account_category_id', 'id');
    }
}
