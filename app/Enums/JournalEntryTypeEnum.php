<?php

namespace App\Enums;

enum JournalEntryTypeEnum: string
{
    case BEGINNING_BALANCES = 'BEGINNING_BALANCES';
    case ADJUSTING_ENTRIES  = 'ADJUSTING_ENTRIES';

    public function label(): string
    {
        return match ($this) {
            self::BEGINNING_BALANCES => 'Saldo Awal',
            self::ADJUSTING_ENTRIES  => 'Lainnya',
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
