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
        Schema::create('autoloader_checks', function (Blueprint $table) {
            $table->id();
            $table->string('nomer_autoloader');
            $table->string('shift');
            $table->string('bulan');
            

            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus
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
