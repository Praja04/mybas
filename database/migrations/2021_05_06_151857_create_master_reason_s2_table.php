<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterReasonS2Table extends Migration
{
    /*
     * Run the migration
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_reason_s2', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipe', 150);
            $table->string('kode_reason', 50);
            $table->string('nama_reason', 255);
            $table->enum('is_active', ['Y', 'N'])->default('Y');
            $table->timestamps();
        });
    }

    /*
     * Reverse the migration
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_reason_s2');
    }
}
