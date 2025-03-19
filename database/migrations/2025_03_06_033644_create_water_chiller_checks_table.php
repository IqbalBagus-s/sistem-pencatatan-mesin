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
        Schema::create('water_chiller_checks', function (Blueprint $table) {
            $table->id(); // Kolom id (primary key)
            $table->date('tanggal'); // Kolom tanggal
            $table->string('hari'); // Kolom hari
            $table->string('checked_by'); // Kolom checked_by
            $table->string('approved_by')->nullable(); // Kolom approved_by (nullable)
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
        Schema::dropIfExists('water_chiller_checks');
    }
};
