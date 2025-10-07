<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGramaturLoggingMachineTable extends Migration
{
    /*
     * Run the migration
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gramatur_logging_machine', function(Blueprint $table) {
            $table->integer('id')->index()->autoIncrement();
            $table->integer('id_logging_machine')->nullable();
            $table->date('tgl_pengisian')->nullable();
            $table->string('jam_ke', 255)->nullable();
            $table->string('sampling_ke', 255)->nullable();
            $table->time('jam_pengisian')->nullable();
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
        Schema::drop('gramatur_logging_machine');
    }
}
