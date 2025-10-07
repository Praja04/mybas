<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSigraSertifikasiSioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sigra_sertifikasi_sio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sio');
            $table->string('nomor_izin', 50);
            $table->date('tanggal_terbit');
            $table->date('tanggal_habis')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status', 20);
            $table->timestamps();
            $table->string('transaction_id', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sigra_sertifikasi_sio');
    }
}
