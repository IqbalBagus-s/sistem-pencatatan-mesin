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
        Schema::create('slitting_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_id'); // Foreign key dari sletting_checks
            $table->string('checked_items')->nullable();
            
            // Data tiap minggu
            for ($i = 1; $i <= 4; $i++) {
                $table->string("minggu{$i}")->nullable();
                $table->string("keterangan_minggu{$i}")->nullable();
            }

            $table->softDeletes(); // Soft delete untuk menyimpan data yang dihapus
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('check_id')->references('id')->on('slitting_checks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sletting_results');
    }
};
