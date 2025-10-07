<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJumlahStockSesudahToJumlahBerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jumlah_beras', function (Blueprint $table) {
            $table->double('jumlah_stock_sesudah')->nullable();
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
            $table->dropColumn('jumlah_stock_sesudah');
        });
    }
}
