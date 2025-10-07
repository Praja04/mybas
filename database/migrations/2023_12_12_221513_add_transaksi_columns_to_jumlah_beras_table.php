<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransaksiColumnsToJumlahBerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jumlah_beras', function (Blueprint $table) {
            $table->boolean('transaksi_in')->default(false);
            $table->boolean('transaksi_out')->default(false);
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
            $table->dropColumn('transaksi_in');
            $table->dropColumn('transaksi_out');
        });
    }
}
