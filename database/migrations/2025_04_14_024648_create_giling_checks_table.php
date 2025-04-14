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
            $table->string('checked_by'); // Kolom checked_by
            $table->string('approved_by1')->nullable(); // Kolom approved_by1 (nullable)
            $table->string('approved_by2')->nullable(); // Kolom approved_by2 (nullable)
            $table->text('keterangan')->nullable(); // Kolom keterangan (text dan nullable)
            $table->timestamps(); // Kolom created_at dan updated_at
            $table->softDeletes();
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
