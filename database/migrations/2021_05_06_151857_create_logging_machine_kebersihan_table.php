<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoggingMachineKebersihanTable extends Migration
{
    /*
     * Run the migration
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logging_machine_kebersihan', function(Blueprint $table) {
            $table->increments('id');
            $table->string('lantai', 25)->nullable();
            $table->string('bak', 25)->nullable();
            $table->string('body_mesin', 25)->nullable();
            $table->string('sealer', 25)->nullable();
            $table->string('gayung', 25)->nullable();
            $table->string('sodokan', 25)->nullable();
            $table->string('tutup_hopper', 25)->nullable();
            $table->string('serbet', 25)->nullable();
            $table->integer('id_logging_machine')->nullable();
            $table->string('nik', 20)->nullable();
            $table->date('tgl_pengisian')->nullable();
        });
    }

    /*
     * Reverse the migration
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('logging_machine_kebersihan');
    }
}
