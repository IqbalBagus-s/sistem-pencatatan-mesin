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
        Schema::create('caplining_checks', function (Blueprint $table) {
            $table->id();
            $table->string('nomer_caplining');
            for ($i = 1; $i <= 5; $i++) {
                $table->string('tanggal_check' . $i)->nullable();
                $table->string('checked_by' . $i)->nullable();
                $table->string('approved_by' . $i)->nullable();
            }
            $table->enum('status', ['disetujui', 'belum_disetujui'])->default('belum_disetujui');
            
            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caplining_checks');
    }
};
