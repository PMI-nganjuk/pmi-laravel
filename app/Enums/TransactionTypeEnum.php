<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case INCOME   = 'PEMASUKAN';
    case EXPENSE = 'PENGELUARAN';
    case ADJUSTMENT = 'PENYESUAIAN';

    public function label(): string
    {
        return match ($this) {
            self::INCOME   => 'Penerimaan Kas',
            self::EXPENSE => 'Pengeluaran Kas',
            self::ADJUSTMENT => 'Penyesuaian',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::INCOME   => 'success',
            self::EXPENSE => 'error',
            self::ADJUSTMENT => 'warning',
        };
    }

    public function documentPrefix(): string
    {
        return match ($this) {
            self::INCOME   => 'BKMUDD',
            self::EXPENSE => 'BKKUDD',
            self::ADJUSTMENT => 'BKJUDD',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn (self $type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ])
            ->toArray();
    }
}
