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
        // Create demo users with their respective roles
        $users = [
            [
                'name' => 'Admin PMI',
                'email' => 'admin@pmi-nganjuk.or.id',
                'password' => 'password',
                'role' => RoleEnum::ADMIN,
            ],
            [
                'name' => 'Manager Keuangan PMI',
                'email' => 'manager@pmi-nganjuk.or.id',
                'password' => 'password',
                'role' => RoleEnum::FINANCIAL_MANAGER,
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
            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'role' => $userData['role'],
            ]);
        }
    }
}
