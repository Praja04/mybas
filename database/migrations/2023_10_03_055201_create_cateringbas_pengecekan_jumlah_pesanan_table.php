<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringbasPengecekanJumlahPesananTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cateringbas_pengecekan_jumlah_pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('id_transaksi')->nullable();
            $table->enum('kategori_staff', ['staff', 'non staff']);
            $table->integer('jumlah_order_bas');
            $table->integer('jumlah_order');
            $table->enum('keterangan', ['sesuai', 'tidak']);
            $table->string('nama_makanan', 255);
            $table->string('foto')->nullable();
            $table->enum('shift', [1, 2, 3]);
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
        Schema::dropIfExists('cateringbas_pengecekan_jumlah_pesanan');
    }
}
