<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInnerLoggingMachineTable extends Migration
{
    /*
     * Run the migration
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inner_logging_machine', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_logging_machine')->nullable();
            $table->string('nik', 50)->nullable();
            $table->string('foto', 250)->nullable();
            $table->date('tgl_pengisian')->nullable();
            $table->date('tgl_edit')->nullable();
            $table->time('jam_pengisian')->nullable();
            $table->time('jam_edit')->nullable();
        });
    }

    /*
     * Reverse the migration
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('inner_logging_machine');
    }
}
