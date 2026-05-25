<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class CategoryTwo extends Model
{
    use HasFactory;

    protected $table = 'category_twos';
    protected $primaryKey = 'category_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'category_code',
        'category_name',
        'category_one',
    ];

    public function categoryOne()
    {
        return $this->belongsTo(CategoryOne::class, 'category_one', 'category_code');
    }
}
