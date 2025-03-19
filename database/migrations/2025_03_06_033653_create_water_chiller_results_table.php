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
            $table->string('no_mesin')->nullable();
            $table->string('Temperatur_Compressor')->nullable(); 
            $table->string('Temperatur_Kabel')->nullable(); 
            $table->string('Temperatur_Mcb')->nullable(); 
            $table->string('Temperatur_Air')->nullable(); 
            $table->string('Temperatur_Pompa')->nullable(); 
            $table->string('Evaporator')->nullable(); 
            $table->string('Fan_Evaporator')->nullable(); 
            $table->string('Freon')->nullable(); 
            $table->string('Air')->nullable(); 

            $table->timestamps(); // Kolom created_at dan updated_at
            $table->softDeletes();

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
