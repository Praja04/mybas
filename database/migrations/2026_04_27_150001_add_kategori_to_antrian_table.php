<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKategoriToAntrianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('antrian_bongkar_muat', function (Blueprint $table) {
            $table->enum('kategori', ['bongkar_muat', 'tamu', 'tkbm'])->default('bongkar_muat')->after('nomor_antrian');
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
            $table->dropColumn('kategori');
        });
    }
}
