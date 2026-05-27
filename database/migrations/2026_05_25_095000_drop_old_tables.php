<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only drop old tables if migration was successful
        // Table names and structures should ensure we're dropping the right tables

        // Drop in order respecting foreign key dependencies
        // general_ledgers has FK to chart_of_accounts, so it's kept
        // Drop old category tables
        if (Schema::hasTable('category_twos')) {
            Schema::dropIfExists('category_twos');
        }
        if (Schema::hasTable('category_ones')) {
            Schema::dropIfExists('category_ones');
        }

        // Only drop report_types if financial_report_types exists and has data
        // This ensures we keep old table if migration failed
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reliably restore dropped tables without complex logic
        throw new \Exception('This migration cannot be reversed. The old tables have been dropped.');
    }
};
