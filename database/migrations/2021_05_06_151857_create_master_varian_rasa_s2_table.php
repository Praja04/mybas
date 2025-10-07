<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterVarianRasaS2Table extends Migration
{
    /*
     * Run the migration
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_varian_rasa_s2', function(Blueprint $table) {
            $table->increments('id');
            $table->string('rasa', 255);
        });
    }

    /*
     * Reverse the migration
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('master_varian_rasa_s2');
    }
}
