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
        Schema::create('crane_matras_checks', function (Blueprint $table) {
            $table->id();
            $table->string('nomer_crane_matras');
            $table->string('bulan');
            $table->string('tanggal')->nullable();
            $table->unsignedBigInteger('checker_id')->nullable();
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->enum('status', ['disetujui', 'belum_disetujui'])->default('belum_disetujui');
            
            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus
            $table->foreign('checker_id')->references('id')->on('checkers')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('approver_id')->references('id')->on('approvers')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crane_matras_checks');
    }
};
