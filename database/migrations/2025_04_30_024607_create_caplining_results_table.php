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
        Schema::create('caplining_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_id'); // Foreign key dari caplining_checks
            $table->string('checked_items')->nullable();
            // Kolom check 1 sampai check 5 dan keterangan 1 sampai keterangan 5
            for ($i = 1; $i <= 5; $i++) {
                $table->string('check' . $i, 30)->nullable();
                $table->string('keterangan' . $i, 30)->nullable();
            }
            
            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus
            // Tambahkan foreign key constraint
            $table->foreign('check_id')->references('id')->on('caplining_checks')->onDelete('cascade'); // jika parent dihapus, data ini ikut terhapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caplining_results');
    }
};
