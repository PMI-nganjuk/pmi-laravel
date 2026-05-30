<?php

namespace App\Models;

use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'document_number',
        'transaction_type',
        'program_id',
        'user_id',
        'reference',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'transaction_type' => TransactionTypeEnum::class,
        ];
    }

    public function generalLedgers(): HasMany
    {
        return $this->hasMany(GeneralLedger::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
