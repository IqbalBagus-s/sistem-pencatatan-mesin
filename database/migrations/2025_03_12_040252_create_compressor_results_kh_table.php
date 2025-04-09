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
        Schema::create('compressor_kh_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_id');
            $table->string('checked_items');
            
            // Kompresor high
            $table->string('kh_7I')->nullable();
            $table->string('kh_7II')->nullable();
            $table->string('kh_8I')->nullable();
            $table->string('kh_8II')->nullable();
            $table->string('kh_9I')->nullable();
            $table->string('kh_9II')->nullable();
            $table->string('kh_10I')->nullable();
            $table->string('kh_10II')->nullable();
            $table->string('kh_11I')->nullable();
            $table->string('kh_11II')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign key
            $table->foreign('check_id')->references('id')->on('compressor_checks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compressor_results');
    }
};

