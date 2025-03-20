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
        Schema::create('hopper_checks', function (Blueprint $table) {
            $table->id();
            $table->string('nomer_hopper'); // Menyimpan nomor hopper
            $table->string('bulan'); // Menyimpan bulan
            $table->date('tanggal')->nullable(); // Bisa bernilai null
            $table->string('checked_by')->nullable(); // Bisa bernilai null
            $table->string('approved_by')->nullable(); // Bisa bernilai null
            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hopper_checks');
    }
};
