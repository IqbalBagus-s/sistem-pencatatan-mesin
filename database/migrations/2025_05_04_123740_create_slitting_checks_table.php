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
        Schema::create('slitting_checks', function (Blueprint $table) {
            $table->id();
            $table->string('nomer_slitting'); // Menyimpan nomor mesin
            $table->string('bulan'); // Menyimpan bulan
            
            // data checker & approver tiap minggu
            for ($i = 1; $i <= 4; $i++) {
                $table->string("checked_by_minggu{$i}")->nullable(); // Bisa bernilai null
                $table->string("approved_by_minggu{$i}")->nullable(); // Bisa bernilai null
            }

            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slitting_checks');
    }
};
