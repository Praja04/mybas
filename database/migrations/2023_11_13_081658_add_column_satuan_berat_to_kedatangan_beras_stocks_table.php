<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSatuanBeratToKedatanganBerasStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kedatangan_beras_stocks', function (Blueprint $table) {
            $table->enum('satuan_berat', ['sak', 'kg', 'hg', 'dag', 'g', 'dg', 'cm', 'mm'])->default('sak');
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
            $table->dropColumn('satuan_berat');
        });
    }
}
