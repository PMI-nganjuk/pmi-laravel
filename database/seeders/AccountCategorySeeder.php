<?php

namespace Database\Seeders;

use App\Models\AccountCategory;
use Illuminate\Database\Seeder;

class AccountCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Aktiva Lancar'],
            ['name' => 'Aset Lancar'],
            ['name' => 'Aset Tidak Lancar'],
            ['name' => 'Liabilitas Jangka Pendek'],
            ['name' => 'Liabilitas Jangka Panjang'],
            ['name' => 'Aset Netto'],
            ['name' => 'Pendapatan'],
            ['name' => 'Beban Program'],
            ['name' => 'Beban Manajemen Umum'],    
        ];

        foreach ($categories as $category) {
            AccountCategory::updateOrCreate(
                ['name' => $category['name']]
            );
        }
    }
}