<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\EntryTypeEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('category_two')
                  ->index()
                  ->constrained(table: 'category_twos', column: 'category_code')
                  ->cascadeOnDelete();
            $table->string('account_name', 100);
            $table->enum('entry_type', array_column(EntryTypeEnum::cases(), 'value'));
            $table->foreignId('report_type_id')
                  ->index()
                  ->constrained(table: 'report_types', column: 'id')
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
