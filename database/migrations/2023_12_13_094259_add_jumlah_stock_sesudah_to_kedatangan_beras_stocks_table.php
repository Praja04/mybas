<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJumlahStockSesudahToKedatanganBerasStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kedatangan_beras_stocks', function (Blueprint $table) {
            $table->integer('jumlah_stock_sesudah')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kedatangan_beras_stocks', function (Blueprint $table) {
            $table->dropColumn('jumlah_stock_sesudah');
        });
    }
}
