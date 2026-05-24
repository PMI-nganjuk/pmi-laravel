<?php

namespace App\Enums;

enum EntryTypeEnum: string
{
    case DEBIT = 'D';
    case CREDIT = 'C';

    public function label(): string
    {
        return match ($this) {
            self::DEBIT => 'Debit',
            self::CREDIT => 'Kredit',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::DEBIT => 'success',
            self::CREDIT => 'error',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn (self $entryType) => [
                'value' => $entryType->value,
                'label' => $entryType->label(),
            ])
            ->toArray();
    }
}