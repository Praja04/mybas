<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPetugasKantinToPengambilanSampelCateringbasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengambilan_sampel_cateringbas', function (Blueprint $table) {
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
        Schema::table('pengambilan_sampel_cateringbas', function (Blueprint $table) {
            $table->dropColumn('nama_petugas_kantin');
        });
    }
}
