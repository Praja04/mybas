<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePkwBagianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkw_bagian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_divisi');
            $table->string('kode_bagian', 10);
            $table->string('nama_bagian', 35);
            $table->string('kabag', 150)->nullable();
            $table->string('cost_center', 50)->nullable();
            $table->timestamps();
            $table->foreign('id_divisi')->references('id')->on('pkw_divisi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pkw_bagian');
    }
}
