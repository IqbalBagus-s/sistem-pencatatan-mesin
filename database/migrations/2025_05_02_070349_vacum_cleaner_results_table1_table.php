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
        Schema::create('vacum_cleaner_results_table1', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_id'); // Foreign key dari vacum_cleaner_checks
            $table->string('checked_items')->nullable();
            $table->string('minggu2')->nullable();
            $table->string('keterangan_minggu2')->nullable();
            
            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus
            // Tambahkan foreign key constraint
            $table->foreign('check_id')->references('id')->on('vacum_cleaner_checks')->onDelete('cascade'); // jika parent dihapus, data ini ikut terhapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacum_cleaner_result_table1s');
    }
};