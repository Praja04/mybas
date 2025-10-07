<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKolomUntukAlamatLengkapToPkwKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pkw_karyawan', function (Blueprint $table) {
            $table->char('alamat_ktp_provinsi', 2)->nullable();
            $table->char('alamat_ktp_kota', 4)->nullable();
            $table->char('alamat_ktp_kecamatan', 7)->nullable();
            $table->char('alamat_ktp_desa', 10)->nullable();
            $table->text('alamat_ktp')->nullable();
            $table->char('alamat_sekarang_provinsi', 2)->nullable();
            $table->char('alamat_sekarang_kota', 4)->nullable();
            $table->char('alamat_sekarang_kecamatan', 7)->nullable();
            $table->char('alamat_sekarang_desa', 10)->nullable();
            $table->text('alamat_sekarang')->nullable();
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
            //
        });
    }
}
