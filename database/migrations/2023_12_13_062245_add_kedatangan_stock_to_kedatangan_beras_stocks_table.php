<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKedatanganStockToKedatanganBerasStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kedatangan_beras_stocks', function (Blueprint $table) {
            $table->integer('kedatangan_stock')->nullable();
            $table->integer('qty_kedatangan_stock')->nullable();
            $table->string('satuan_berat')->nullable();
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
            $table->dropColumn('kedatangan_stock');
            $table->dropColumn('qty_kedatangan_stock');
            $table->dropColumn('satuan_berat');
        });
    }
}
