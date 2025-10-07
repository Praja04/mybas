<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasaSimpanToPengambilanSampelCateringbasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengambilan_sampel_cateringbas', function (Blueprint $table) {
            $table->string('masa_simpan')->nullable();
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
            $table->dropColumn('masa_simpan');
        });
    }
}
