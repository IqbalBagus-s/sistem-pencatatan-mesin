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
        Schema::create('compressor_kl_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_id');
            $table->string('checked_items');
            
            // Kompresor low
            $table->string('kl_10I')->nullable();
            $table->string('kl_10II')->nullable();
            $table->string('kl_5I')->nullable();
            $table->string('kl_5II')->nullable();
            $table->string('kl_6I')->nullable();
            $table->string('kl_6II')->nullable();
            $table->string('kl_7I')->nullable();
            $table->string('kl_7II')->nullable();
            $table->string('kl_8I')->nullable();
            $table->string('kl_8II')->nullable();
            $table->string('kl_9I')->nullable();
            $table->string('kl_9II')->nullable();

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

