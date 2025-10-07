<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQtyPengambilanToBerasPengambilansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beras_pengambilans', function (Blueprint $table) {
            $table->double('qty_pengambilan')->after('jumlah_pengambilan'); // Sesuaikan tipe datanya
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
            $table->dropColumn('qty_pengambilan');
        });
    }
}
