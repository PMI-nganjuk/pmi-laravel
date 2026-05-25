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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('document_number')->unique(); // Contoh: BKMUDD001, BKKUDD001, BKJUDD001
            $table->enum('transaction_type', ['PEMASUKAN', 'PENGELUARAN', 'PENYESUAIAN']); 
            $table->foreignId('program_id')->index()->nullable()->constrained('programs')->cascadeOnDelete(); 
            $table->foreignId('user_id')->index()->constrained('users')->cascadeOnDelete();
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
