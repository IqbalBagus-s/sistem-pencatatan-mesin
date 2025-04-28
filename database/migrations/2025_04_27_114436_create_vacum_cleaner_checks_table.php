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
        Schema::create('vacum_cleaner_checks', function (Blueprint $table) {
            $table->id();
            $table->string('nomer_vacum_cleaner');
            $table->string('bulan');
            $table->string('tanggal_dibuat')->nullable();
            $table->string('checker_minggu1')->nullable();
            $table->string('checker_minggu2')->nullable();
            $table->string('approver_minggu1')->nullable();
            $table->string('approver_minggu2')->nullable();
            

            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacum_cleaner_checks');
    }
};
