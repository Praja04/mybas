<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterPlanningS2Table extends Migration
{
    /*
     * Run the migration
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_planning_s2', function(Blueprint $table) {
            $table->increments('id');
            $table->string('group', 100)->nullable();
            $table->string('varian_rasa', 100)->nullable();
            $table->integer('target_pcs')->nullable();
            $table->integer('target_box')->nullable();
        });
    }

    /*
     * Reverse the migration
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('master_planning_s2');
    }
}
