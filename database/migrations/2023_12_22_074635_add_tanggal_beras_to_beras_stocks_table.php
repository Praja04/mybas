<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTanggalBerasToBerasStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kedatangan_beras_stocks', function (Blueprint $table) {
            $table->date('tanggal_beras')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beras_stocks', function (Blueprint $table) {
            $table->dropColumn('tanggal_beras');
        });
    }
}
