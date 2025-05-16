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
        Schema::create('crane_matras_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_id'); // Foreign key dari crane_matras_checks
            $table->string('checked_items')->nullable();
            $table->string('check')->nullable();
            $table->string('keterangan')->nullable();

            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus
            $table->timestamps();
            // Foreign key constraint
            $table->foreign('check_id')->references('id')->on('crane_matras_checks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crane_matras_results_table1s');
    }
};
