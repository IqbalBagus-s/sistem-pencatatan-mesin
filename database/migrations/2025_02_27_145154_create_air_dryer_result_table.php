<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('air_dryer_result', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_id');
            $table->string('nomor_mesin');
            $table->string('temperatur_kompresor');
            $table->string('temperatur_kabel');
            $table->string('temperatur_mcb');
            $table->string('temperatur_angin_in');
            $table->string('temperatur_angin_out');
            $table->string('evaporator');
            $table->string('fan_evaporator');
            $table->string('auto_drain');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('check_id')->references('id')->on('air_dryer_checks')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('air_dryer_result');
    }
};
