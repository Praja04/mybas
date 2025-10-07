<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransaksiKeluarMasukToJumlahBerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jumlah_beras', function (Blueprint $table) {
            $table->float('transaksi_keluar')->nullable();
            $table->float('transaksi_masuk')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jumlah_beras', function (Blueprint $table) {
            $table->dropColumn(['transaksi_keluar', 'transaksi_masuk']);
        });
    }
}
