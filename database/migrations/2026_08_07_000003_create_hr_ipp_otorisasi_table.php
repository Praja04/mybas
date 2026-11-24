<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrIppOtorisasiTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_ipp_otorisasi')) {
            Schema::create('hr_ipp_otorisasi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ipp_id');
                $table->string('nik_yang_bersangkutan', 50);
                $table->string('nama_yang_bersangkutan', 150)->nullable();
                $table->string('nik_manager', 50)->nullable();
                $table->string('nama_manager', 150)->nullable();
                $table->string('nik_associate_manager', 50)->nullable();
                $table->string('nama_associate_manager', 150)->nullable();
                $table->string('send_by_username', 100)->nullable();
                $table->integer('jumlah_bawahan')->nullable();
                $table->timestamps();

                $table->foreign('ipp_id')->references('id')->on('hr_ipp')->onDelete('cascade');
                $table->index('ipp_id', 'hr_ipp_otorisasi_ipp_idx');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('hr_ipp_otorisasi');
    }
}
