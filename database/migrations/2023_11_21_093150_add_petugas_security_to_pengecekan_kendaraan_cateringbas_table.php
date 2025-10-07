<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPetugasSecurityToPengecekanKendaraanCateringbasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengecekan_kendaraan_cateringbas', function (Blueprint $table) {
            $table->string('nama_petugas_security')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengecekan_kendaraan_cateringbas', function (Blueprint $table) {
            $table->dropColumn('nama_petugas_security');
        });
    }
}
