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
        Schema::create('approvers', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique(); // Username harus unik
            $table->string('password'); // Hash password (gunakan bcrypt saat menyimpan)
            $table->string('role'); // Tambahan atribut role
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvers');
    }
};
