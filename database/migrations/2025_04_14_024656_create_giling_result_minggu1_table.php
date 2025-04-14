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
        Schema::create('giling_result_minggu1', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_id'); // Foreign key dari giling_checks
            $table->string('checked_items')->nullable();
            // data permesin
            $table->string('g1')->nullable();
            $table->string('g2')->nullable();
            $table->string('g3')->nullable();
            $table->string('g4')->nullable();
            $table->string('g5')->nullable();
            $table->string('g6')->nullable();
            $table->string('g7')->nullable();
            $table->string('g8')->nullable();
            $table->string('g9')->nullable();
            $table->string('g10')->nullable();
            
            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus

            // Foreign key constraint
            $table->foreign('check_id')->references('id')->on('giling_checks')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giling_results');
    }
};
