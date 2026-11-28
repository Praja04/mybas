<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveWaktuDilayaniFromAntrianBongkarMuatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('antrian_bongkar_muat', function (Blueprint $table) {
            $table->dropColumn('waktu_dilayani');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('antrian_bongkar_muat', function (Blueprint $table) {
            $table->timestamp('waktu_dilayani')->nullable();
        });
    }
}
