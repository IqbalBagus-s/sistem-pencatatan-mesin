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
        Schema::create('dehum_matras_results_table3', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_id'); // Foreign key dari dehum_matras_checks
            $table->string('checked_items')->nullable();
            // Kolom tanggal1 sampai tanggal11 
            for ($i = 23; $i <= 31; $i++) {
                $table->string('tanggal' . $i, 30)->nullable();
            }

            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus
            // Tambahkan foreign key constraint
            $table->foreign('check_id')->references('id')->on('dehum_matras_checks')->onDelete('cascade'); // jika parent dihapus, data ini ikut terhapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dehum_matras_result_table3s');
    }
};