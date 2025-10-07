<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGolonganDarahColumnToPkwKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pkw_karyawan', function (Blueprint $table) {
            $table->string('golongan_darah', 5)->after('tanggal_lahir')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pkw_karyawan', function (Blueprint $table) {
            $table->dropColumn('golongan_darah');
        });
    }
}
