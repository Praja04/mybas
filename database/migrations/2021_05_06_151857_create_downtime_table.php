<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDowntimeTable extends Migration
{
    /*
     * Run the migration
     *
     * @return void
     */
    public function up()
    {
        Schema::create('downtime', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_logging_machine')->nullable();
            $table->date('tgl_pengisian')->nullable();
            $table->time('jam_pengisian')->nullable();
            $table->string('kerusakan', 500)->nullable();
            $table->string('jenis_maintenance', 50)->nullable();
            $table->integer('approval_maintenance')->nullable();
            $table->string('approval_maintenance_nama', 50)->nullable();
            $table->string('approval_maintenance_nik', 50)->nullable();
            $table->date('tgl_respon_maintenance')->nullable();
            $table->time('jam_mulai_maintenance')->nullable();
            $table->time('jam_selesai_maintenance')->nullable();
            $table->string('approval_maintenance_remarks', 500)->nullable();
            $table->integer('status')->nullable();
            $table->string('progress', 100)->nullable();
            $table->string('nik', 50)->nullable();
            $table->string('no_mesin', 50)->nullable();
            $table->integer('close_operator')->nullable();
        });
    }

    /*
     * Reverse the migration
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('downtime');
    }
}
