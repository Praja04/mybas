<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterRangeSamplingS2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_range_sampling_s2', function (Blueprint $table) {
            $table->id();
            $table->string('varian', 100);
            $table->integer('isi_box');
            $table->integer('isi_pallet');
            $table->integer('qty_box');
            $table->string('minimum', 50);
            $table->string('maksimum', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_range_sampling_s2');
    }
}
