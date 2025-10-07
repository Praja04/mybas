<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterMesinS2Table extends Migration
{
    /*
     * Run the migration
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_mesin_s2', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('line');
            $table->string('group', 50);
            $table->string('no_mesin', 255)->nullable();
            $table->string('jenis_mesin', 50)->nullable();
            $table->integer('NoSeq')->nullable();
        });
    }

    /*
     * Reverse the migration
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('master_mesin_s2');
    }
}
