<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPetugasKantinToCateringbasPengecekanJumlahPesananTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cateringbas_pengecekan_jumlah_pesanan', function (Blueprint $table) {
            $table->string('nama_petugas_kantin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cateringbas_pengecekan_jumlah_pesanan', function (Blueprint $table) {
            $table->dropColumn('nama_petugas_kantin');
        });
    }
}
