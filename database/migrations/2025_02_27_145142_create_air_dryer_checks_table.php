<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('air_dryer_checks', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('hari');
            $table->string('checked_by');
            $table->string('approved_by')->nullable(); // Dibuat nullable
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('air_dryer_checks');
    }
};
