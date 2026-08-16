<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin PMI',
                'email' => 'admin@pmi-nganjuk.or.id',
                'password' => 'password',
                'role' => RoleEnum::ADMIN,
            ],
            [
                'name' => 'Staf Keuangan PMI',
                'email' => 'stafkeuangan@pmi-nganjuk.or.id',
                'password' => 'password',
                'role' => RoleEnum::FINANCE_STAFF,
            ],
            [
                'name' => 'Karyawan PMI',
                'email' => 'karyawan@pmi-nganjuk.or.id',
                'password' => 'password',
                'role' => RoleEnum::STAFF,
            ],
            [
                'name' => 'Pengguna Umum PMI',
                'email' => 'pengguna@pmi-nganjuk.or.id',
                'password' => 'password',
                'role' => RoleEnum::USER,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                    'role' => $userData['role'],
                ]
            );
        }
    }
}
