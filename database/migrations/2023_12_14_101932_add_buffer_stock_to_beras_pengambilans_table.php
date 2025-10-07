<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBufferStockToBerasPengambilansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beras_pengambilans', function (Blueprint $table) {
            $table->double('jumlah_pengambilan_sebelum')->nullable();
            $table->double('transaksi_masuk')->nullable();
            $table->double('transaksi_keluar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beras_pengambilans', function (Blueprint $table) {
            $table->dropColumn('jumlah_pengambilan_sebelum');
            $table->dropColumn('transaksi_masuk');
            $table->dropColumn('transaksi_keluar');
        });
    }
}
