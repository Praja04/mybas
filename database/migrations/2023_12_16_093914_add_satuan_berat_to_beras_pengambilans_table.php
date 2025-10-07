<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSatuanBeratToBerasPengambilansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beras_pengambilans', function (Blueprint $table) {
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
        Schema::table('beras_pengambilans', function (Blueprint $table) {
            $table->dropColumn('satuan_berat');
        });
    }
}
