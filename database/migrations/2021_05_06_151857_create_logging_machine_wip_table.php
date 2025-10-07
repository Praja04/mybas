<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoggingMachineWipTable extends Migration
{
    /*
     * Run the migration
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logging_machine_wip', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_logging_machine')->nullable();
            $table->string('rasa', 100)->nullable();
            $table->double('inner_reject', 0, 0)->nullable();
            $table->double('sampah_inner', 0, 0)->nullable();
            $table->integer('total_wip')->nullable();
            $table->integer('sortir')->nullable();
            $table->integer('sobek')->nullable();
            $table->date('tgl_pengisian')->nullable();
            $table->string('nik', 50)->nullable();
        });
    }

    /*
     * Reverse the migration
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('logging_machine_wip');
    }
}
