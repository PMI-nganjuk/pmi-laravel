<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->index('account_name');
            $table->index('entry_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropIndex(['account_name']);
            $table->dropIndex(['entry_type']);
        });
    }
};
