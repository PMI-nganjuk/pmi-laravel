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
            $table->foreignId('account_subcategory_id')->index()->constrained('account_subcategories')->cascadeOnDelete();
            $table->string('account_name', 100);
            $table->enum('normal_balance', array_column(EntryTypeEnum::cases(), 'value'));
            $table->foreignId('financial_report_type_id')->index()->constrained('financial_report_types')->cascadeOnDelete();
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
