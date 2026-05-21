<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'Admin';
    case FINANCIAL_MANAGER = 'Manager keuangan';
    case FINANCE_STAFF = 'Staf Keuangan';
    case STAFF = 'Karyawan';
    case USER = 'Pengguna Umum';

    public function getLabel(): ?string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
