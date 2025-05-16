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
        Schema::create('autoloader_result_table1', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_id'); // Foreign key dari autoloader_checks
            $table->string('checked_items')->nullable();
            // Kolom tanggal1 sampai tanggal11 dan keterangan_tanggal1 sampai keterangan_tanggal11
            for ($i = 1; $i <= 11; $i++) {
                $table->string('tanggal' . $i, 30)->nullable();
                $table->string('keterangan_tanggal' . $i, 30)->nullable();
            }

            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus        

            // Tambahkan foreign key constraint
            $table->foreign('check_id')->references('id')->on('autoloader_checks')->onDelete('cascade'); // jika parent dihapus, data ini ikut terhapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autoloader_result_table1s');
    }
};
