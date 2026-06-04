<?php

namespace Database\Seeders;

use App\Models\FinancialReportType;
use Illuminate\Database\Seeder;

class FinancialReportTypeSeeder extends Seeder
{
    /**
     * Seed tipe laporan keuangan.
     */
    public function run(): void
    {
        $types = [
            ['id' => 1, 'name' => 'Neraca'],
            ['id' => 2, 'name' => 'Laba Rugi'],
        ];

        foreach ($types as $type) {
            FinancialReportType::updateOrCreate(
                ['id' => $type['id']],
                ['name' => $type['name']]
            );
        }
    }
}
