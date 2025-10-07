<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdjustmentPengambilanToBerasPengambilansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beras_pengambilans', function (Blueprint $table) {
            $table->double('adjustment_pengambilan', 8, 2)->nullable();
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
            $table->dropColumn('adjustment_pengambilan');
        });
    }
}
