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
        Schema::create('compressor_checks', function (Blueprint $table) {
            $table->id(); // Kolom id (primary key)
            $table->date('tanggal'); // Kolom tanggal
            $table->string('hari'); // Kolom hari
            $table->string('checked_by_shift1')->nullable(); // Kolom checked_by
            $table->string('checked_by_shift2')->nullable(); // Kolom checked_by
            $table->string('approved_by_shift1')->nullable(); // Kolom approved_by (nullable)
            $table->string('approved_by_shift2')->nullable(); // Kolom approved_by (nullable)
            // jumlah
            $table->text('kompressor_on_kl')->nullable(); 
            $table->text('kompressor_on_kh')->nullable(); 
            $table->text('mesin_on')->nullable(); 
            $table->text('mesin_off')->nullable(); 
            // kelembapan udara
            $table->text('temperatur_shift1')->nullable(); 
            $table->text('tempertatur_shift2')->nullable(); 
            // humidity
            $table->text('humidity_shift1')->nullable(); 
            $table->text('humidity_shift2')->nullable(); 
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compressor_checks');
    }
};
