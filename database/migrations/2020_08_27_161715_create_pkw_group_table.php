<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePkwGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkw_group', function (Blueprint $table) {
            $table->id();
            $table->string('kode_group', 30);
            $table->string('nama_group', 50);
            $table->timestamps();
//            $table->foreign('id_bagian')->references('id')->on('pkw_bagian');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pkw_group');
    }
}
