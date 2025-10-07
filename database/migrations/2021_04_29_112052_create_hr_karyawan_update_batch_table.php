<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrKaryawanUpdateBatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_karyawan_update_batch', function (Blueprint $table) {
            $table->id();
            $table->string('keterangan', 250);
            $table->string('created_by', 150);
            $table->integer('all_count');
            $table->integer('updated_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_karyawan_update_batch');
    }
}
