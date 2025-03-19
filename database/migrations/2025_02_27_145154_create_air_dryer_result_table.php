<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('air_dryer_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_id');
            $table->string('nomor_mesin')->nullable();
            $table->string('temperatur_kompresor')->nullable();
            $table->string('temperatur_kabel')->nullable();
            $table->string('temperatur_mcb')->nullable();
            $table->string('temperatur_angin_in')->nullable();
            $table->string('temperatur_angin_out')->nullable();
            $table->string('evaporator')->nullable();
            $table->string('fan_evaporator')->nullable();
            $table->string('auto_drain')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key
            $table->foreign('check_id')->references('id')->on('air_dryer_checks')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('air_dryer_result');
    }
};
