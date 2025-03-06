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
            $table->string('CH1'); // Kolom CH1
            $table->string('CH2'); // Kolom CH2
            $table->string('CH3'); // Kolom CH3
            $table->string('CH4'); // Kolom CH4
            $table->string('CH5'); // Kolom CH5
            $table->string('CH6'); // Kolom CH6
            $table->string('CH7'); // Kolom CH7
            $table->string('CH8'); // Kolom CH8
            $table->string('CH9'); // Kolom CH9
            $table->string('CH10'); // Kolom CH10
            $table->string('CH11'); // Kolom CH11
            $table->string('CH12'); // Kolom CH12
            $table->string('CH13'); // Kolom CH13
            $table->string('CH14'); // Kolom CH14
            $table->string('CH15'); // Kolom CH15
            $table->string('CH16'); // Kolom CH16
            $table->string('CH17'); // Kolom CH17
            $table->string('CH18'); // Kolom CH18
            $table->string('CH19'); // Kolom CH19
            $table->string('CH20'); // Kolom CH20
            $table->string('CH21'); // Kolom CH21
            $table->string('CH22'); // Kolom CH22
            $table->string('CH23'); // Kolom CH23
            $table->string('CH24'); // Kolom CH24
            $table->string('CH25'); // Kolom CH25
            $table->string('CH26'); // Kolom CH26
            $table->string('CH27'); // Kolom CH27
            $table->string('CH28'); // Kolom CH28
            $table->string('CH29'); // Kolom CH29
            $table->string('CH30'); // Kolom CH30
            $table->string('CH31'); // Kolom CH31
            $table->string('CH32'); // Kolom CH32
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
