<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoggingMachineNoProdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logging_machine_no_prod', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('prod_order');
            $table->string('varian', 150);
            $table->string('shift', 10);
            $table->string('group', 10);
            $table->string('no_mesin', 10);
            $table->date('tgl_pengisian');
            $table->time('jam_pengisian');
            $table->string('nik_pembuat', 50);
            $table->string('nama_pembuat', 50);
            $table->time('jam_edit');
            $table->date('tgl_edit');
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logging_machine_no_prod');
    }
}
