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
            $table->unsignedBigInteger('checker_id_minggu1')->nullable();
            $table->unsignedBigInteger('checker_id_minggu2')->nullable();
            $table->unsignedBigInteger('checker_id_minggu3')->nullable();
            $table->unsignedBigInteger('checker_id_minggu4')->nullable();
            // data approver tiap minggu
            $table->unsignedBigInteger('approver_id_minggu1')->nullable();
            $table->unsignedBigInteger('approver_id_minggu2')->nullable();
            $table->unsignedBigInteger('approver_id_minggu3')->nullable();
            $table->unsignedBigInteger('approver_id_minggu4')->nullable();
            $table->enum('status', ['disetujui', 'belum_disetujui'])->default('belum_disetujui');
            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus
            $table->timestamps();

            $table->foreign('checker_id_minggu1')->references('id')->on('checkers');
            $table->foreign('checker_id_minggu2')->references('id')->on('checkers');
            $table->foreign('checker_id_minggu3')->references('id')->on('checkers');
            $table->foreign('checker_id_minggu4')->references('id')->on('checkers');
            $table->foreign('approver_id_minggu1')->references('id')->on('approvers');
            $table->foreign('approver_id_minggu2')->references('id')->on('approvers');
            $table->foreign('approver_id_minggu3')->references('id')->on('approvers');
            $table->foreign('approver_id_minggu4')->references('id')->on('approvers');
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
