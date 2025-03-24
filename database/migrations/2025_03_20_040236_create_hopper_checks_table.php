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
            // data tanggal tiap minggu
            $table->date('tanggal_minggu1')->nullable(); // Bisa bernilai null
            $table->date('tanggal_minggu2')->nullable(); // Bisa bernilai null
            $table->date('tanggal_minggu3')->nullable(); // Bisa bernilai null
            $table->date('tanggal_minggu4')->nullable(); // Bisa bernilai null
            // data checker tiap minggu
            $table->string('checked_by_minggu1')->nullable(); // Bisa bernilai null
            $table->string('checked_by_minggu2')->nullable(); // Bisa bernilai null
            $table->string('checked_by_minggu3')->nullable(); // Bisa bernilai null
            $table->string('checked_by_minggu4')->nullable(); // Bisa bernilai null
            // data approver tiap minggu
            $table->string('approved_by_minggu1')->nullable(); // Bisa bernilai null
            $table->string('approved_by_minggu2')->nullable(); // Bisa bernilai null
            $table->string('approved_by_minggu3')->nullable(); // Bisa bernilai null
            $table->string('approved_by_minggu4')->nullable(); // Bisa bernilai null
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
