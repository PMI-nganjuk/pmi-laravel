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
        Schema::create('organization_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name')->nullable();
            $table->string('address')->nullable();
            $table->string('chairperson')->nullable();
            $table->string('headquarters_treasurer')->nullable();
            $table->string('blood_donation_unit_treasurer')->nullable();
            $table->date('financial_period_start')->nullable();
            $table->date('financial_period_end')->nullable();
            $table->integer('fiscal_year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_profiles');
    }
};
