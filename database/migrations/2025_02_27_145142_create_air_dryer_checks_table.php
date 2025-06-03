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
            $table->unsignedBigInteger('checker_id')->nullable();
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->enum('status', ['disetujui', 'belum_disetujui'])->default('belum_disetujui');
            $table->text('keterangan')->nullable();

            $table->foreign('checker_id')->references('id')->on('checkers');
            $table->foreign('approver_id')->references('id')->on('approvers');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('air_dryer_checks');
    }
};
