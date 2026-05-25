<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class CategoryOne extends Model
{
    use HasFactory;

    protected $table = 'category_ones';
    protected $primaryKey = 'category_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'category_code',
        'category_name',
    ];

    public function categoryTwo()
    {
        return $this->hasMany(CategoryTwo::class, 'category_code', 'category_code');
    }
}
