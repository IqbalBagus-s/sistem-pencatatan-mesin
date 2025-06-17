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
        Schema::create('autoloader_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanggal_check_id'); // Foreign key dari autoloader_checks
            $table->string('tanggal')->nullable();
            $table->unsignedBigInteger('checker_id')->nullable();
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->enum('status', ['disetujui', 'belum_disetujui'])->default('belum_disetujui');

            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus
            // Tambahkan foreign key constraint
            $table->foreign('tanggal_check_id')->references('id')->on('autoloader_checks')->onDelete('cascade'); // jika parent dihapus, data ini ikut terhapus
            $table->foreign('checker_id')->references('id')->on('checkers');
            $table->foreign('approver_id')->references('id')->on('approvers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autoloader_checks');
    }
};
