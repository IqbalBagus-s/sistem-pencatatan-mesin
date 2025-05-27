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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('user_type'); // 'checker', 'approver', 'host'
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('action'); // 'created', 'updated', 'deleted', 'approved', 'rejected'
            $table->text('description');
            $table->string('target_type')->nullable(); // 'form', 'approver', 'checker'
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('details')->nullable();
            $table->timestamps();

            // Indexes untuk performa
            $table->index(['user_type', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
