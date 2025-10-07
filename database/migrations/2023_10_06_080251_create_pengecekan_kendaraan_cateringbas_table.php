<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengecekanKendaraanCateringbasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengecekan_kendaraan_cateringbas', function (Blueprint $table) {
            $table->id();
            $table->string('id_transaksi')->nullable();
            $table->boolean('fisik_dinding_sebelah_kanan');
            $table->boolean('fisik_dinding_sebelah_kiri');
            $table->boolean('fisik_kondisi_atap');
            $table->boolean('fisik_kondisi_lantai');
            $table->boolean('kebersihan_dinding_sebelah_kanan');
            $table->boolean('kebersihan_dinding_sebelah_kiri');
            $table->boolean('kebersihan_kondisi_atap');
            $table->boolean('kebersihan_kondisi_lantai');
            $table->boolean('ditemukan_barang_lain_diluar_kebutuhan_catering');
            $table->boolean('saat_penerimaan_pintu_keadaan_tertutup');
            $table->boolean('saat_penerimaan_pintu_keadaan_terkunci');
            $table->boolean('box_makanan_dalam_keadaan_tertutup');
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
        Schema::dropIfExists('pengecekan_kendaraan_cateringbas');
    }
}
