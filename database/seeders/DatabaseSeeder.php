<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AccountCategory;
use App\Models\AccountSubcategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AccountCategorySeeder::class);
        $this->call(AccountSubcategorySeeder::class);
        $this->call(FinancialReportTypeSeeder::class);
        $this->call(ChartOfAccountSeeder::class);
        $this->call(RoleAndPermissionSeeder::class);
    }
}
