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
        Schema::create('water_chiller_results', function (Blueprint $table) {
            $table->id(); // Kolom id (primary key)
            $table->unsignedBigInteger('check_id'); 
            $table->string('checked_items'); // Kolom checked_items
            
            // Kolom CH1 sampai CH32 dibuat nullable
            for ($i = 1; $i <= 32; $i++) {
                $table->string("CH$i")->nullable();
            }

            $table->timestamps(); // Kolom created_at dan updated_at

            // Foreign key ke water_chiller_checks
            $table->foreign('check_id')->references('id')->on('water_chiller_checks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_chiller_results');
    }
};
