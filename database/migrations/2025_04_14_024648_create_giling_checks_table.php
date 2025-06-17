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
        Schema::create('giling_checks', function (Blueprint $table) {
            $table->id(); // Kolom id (primary key)
            $table->string('bulan'); // Kolom bulan
            $table->string('minggu'); // Kolom hari
            $table->unsignedBigInteger('checker_id')->nullable();
            $table->unsignedBigInteger('approver_id1')->nullable();
            $table->date('approval_date1')->nullable(); // tanggal approved_by1 (nullable)
            $table->unsignedBigInteger('approver_id2')->nullable();
            $table->enum('status', ['disetujui', 'belum_disetujui'])->default('belum_disetujui');
            $table->text('keterangan')->nullable(); // Kolom keterangan (text dan nullable)
            $table->timestamps(); // Kolom created_at dan updated_at
            $table->softDeletes();

            $table->foreign('checker_id')->references('id')->on('checkers');
            $table->foreign('approver_id1')->references('id')->on('approvers');
            $table->foreign('approver_id2')->references('id')->on('approvers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giling_checks');
    }
};
