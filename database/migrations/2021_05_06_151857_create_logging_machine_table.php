<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoggingMachineTable extends Migration
{
    /*
     * Run the migration
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logging_machine', function(Blueprint $table) {
            $table->integer('id')->index()->autoIncrement();
            $table->string('nama', 100)->nullable();
            $table->string('nik', 50)->nullable();
            $table->string('varian', 100)->nullable();
            $table->string('rasa', 100)->nullable();
            $table->string('no_mesin', 50)->nullable();
            $table->string('shift_group', 50)->nullable();
            $table->date('tgl_pengisian')->nullable();
            $table->integer('total_produksi_box')->nullable();
            $table->integer('hasil_produksi_pcs')->nullable();
            $table->string('kondisi_gear', 25)->nullable();
            $table->integer('approval_spv')->nullable();
            $table->string('approval_spv_nama', 50)->nullable();
            $table->string('approval_spv_nik', 50)->nullable();
            $table->datetime('approval_spv_date')->nullable();
            $table->integer('approval_spv_downtime')->nullable();
            $table->time('jam_mulai_downtime')->nullable();
            $table->time('jam_selesai_downtime')->nullable();
        });
    }

    /*
     * Reverse the migration
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('logging_machine');
    }
}
