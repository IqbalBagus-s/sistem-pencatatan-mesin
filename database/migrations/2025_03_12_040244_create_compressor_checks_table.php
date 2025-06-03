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
            $table->unsignedBigInteger('checker_shift1_id')->nullable();
            $table->unsignedBigInteger('checker_shift2_id')->nullable();
            $table->unsignedBigInteger('approver_shift1_id')->nullable();
            $table->unsignedBigInteger('approver_shift2_id')->nullable();
            
            $table->enum('status', ['disetujui', 'belum_disetujui'])->default('belum_disetujui');
            // jumlah
            $table->string('kompressor_on_kl')->nullable(); 
            $table->string('kompressor_on_kh')->nullable(); 
            $table->string('mesin_on')->nullable(); 
            $table->string('mesin_off')->nullable(); 
            // kelembapan udara
            $table->string('temperatur_shift1')->nullable(); 
            $table->string('temperatur_shift2')->nullable(); 
            // humidity
            $table->string('humidity_shift1')->nullable(); 
            $table->string('humidity_shift2')->nullable(); 
            $table->timestamps(); // Kolom created_at dan updated_at
            $table->softDeletes();

            $table->foreign('checker_shift1_id')->references('id')->on('checkers');
            $table->foreign('checker_shift2_id')->references('id')->on('checkers');
            $table->foreign('approver_shift1_id')->references('id')->on('approvers');
            $table->foreign('approver_shift2_id')->references('id')->on('approvers');
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
