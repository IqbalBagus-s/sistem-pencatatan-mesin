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
            $table->string('tanggal');
            $table->string('checked_by')->nullable();
            $table->string('approved_by')->nullable();
            

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
