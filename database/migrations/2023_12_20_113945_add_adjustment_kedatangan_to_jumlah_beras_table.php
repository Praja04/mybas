<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdjustmentKedatanganToJumlahBerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jumlah_beras', function (Blueprint $table) {
            $table->double('adjustment_kedatangan', 8, 2)->nullable();
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
            $table->dropColumn('adjustment_kedatangan');
        });
    }
}
