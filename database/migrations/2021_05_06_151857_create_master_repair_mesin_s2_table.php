<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterRepairMesinS2Table extends Migration
{
    /*
     * Run the migration
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_repair_mesin_s2', function(Blueprint $table) {
            $table->increments('id');
            $table->string('jenis_mesin', 100)->nullable();
            $table->string('no_mesin', 50)->nullable();
            $table->string('reason', 255)->nullable();
            $table->string('repair', 255)->nullable();
            $table->string('kategori', 50)->nullable();
        });
    }

    /*
     * Reverse the migration
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('master_repair_mesin_s2');
    }
}
