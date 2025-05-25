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
        Schema::table('air_dryer_checks', function (Blueprint $table) {
            $table->enum('status', ['disetujui', 'belum_disetujui'])
                ->default('belum_disetujui')
                ->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('air_dryer_checks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
