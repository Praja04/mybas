<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengambilanSampelCateringbasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengambilan_sampel_cateringbas', function (Blueprint $table) {
            $table->id();
            $table->string('id_transaksi')->nullable();
            $table->dateTime('tanggal_jam_masuk')->nullable();
            $table->dateTime('tanggal_jam_keluar')->nullable();
            $table->string('foto_before')->nullable();
            $table->string('foto_after')->nullable();
            $table->enum('keterangan', ['baik', 'tidak']);
            $table->string('keterangan_menu');
            $table->enum('kategori_staff', ['staff', 'non staff']);
            $table->timestamps();

            $table->foreign('id_transaksi')->references('id')->on('pengirim_cateringbas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengambilan_sampel_cateringbas');
    }
}
