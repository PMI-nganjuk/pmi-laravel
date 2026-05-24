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
        Schema::create('category_twos', function (Blueprint $table) {
            $table->id('category_code');
            $table->string('category_name', 100);
            $table->foreignId('category_one')
                  ->index()
                  ->constrained(table: 'category_ones', column: 'category_code')
                  ->cascadeOnDelete();
            $table->timestamps(); 
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_twos');
    }
};
