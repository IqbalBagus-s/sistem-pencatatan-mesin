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
        Schema::create('hopper_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_id'); // Foreign key dari hopper_checks
            $table->string('checked_items')->nullable();
            
            // Data tiap minggu
            $table->string('minggu1')->nullable();
            $table->string('keterangan_minggu1')->nullable();
            $table->string('minggu2')->nullable();
            $table->string('keterangan_minggu2')->nullable();
            $table->string('minggu3')->nullable();
            $table->string('keterangan_minggu3')->nullable();
            $table->string('minggu4')->nullable();
            $table->string('keterangan_minggu4')->nullable();

            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('check_id')->references('id')->on('hopper_checks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hopper_results');
    }
};
