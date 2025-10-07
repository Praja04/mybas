<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPenilaianPendampingToMasterMakananPendampingCateringbasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_makanan_pendamping_cateringbas', function (Blueprint $table) {
            $table->boolean('baik')->nullable();
            $table->boolean('tidak_baik')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_makanan_pendamping_cateringbas', function (Blueprint $table) {
            $table->dropColumn(['baik', 'tidak_baik']);
        });
    }
}
