<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePkwPengalamanKerjaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkw_pengalaman_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan');
            $table->string('nama_perusahaan', 150);
            $table->string('jabatan', 150)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('kota', 150)->nullable();
            $table->timestamps();
            $table->foreign('id_karyawan')->references('id')->on('pkw_karyawan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pkw_pengalaman_kerja');
    }
}
