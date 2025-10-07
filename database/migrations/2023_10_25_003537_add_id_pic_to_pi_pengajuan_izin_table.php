<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdPicToPiPengajuanIzinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pi_pengajuan_izin', function (Blueprint $table) {
            $table->string('id_pic');
            $table->foreign('id_pic')->references('id_pic')->on('pi_pic')->onDelete('cascade');
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
            $table->dropColumn('id_pic');
        });
    }
}
