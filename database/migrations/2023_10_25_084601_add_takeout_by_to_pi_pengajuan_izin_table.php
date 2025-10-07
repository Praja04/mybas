<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTakeoutByToPiPengajuanIzinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pi_pengajuan_izin', function (Blueprint $table) {
            $table->string('takeout_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pi_pengajuan_izin', function (Blueprint $table) {
            $table->dropColumn('takeout_by');
        });
    }
}
