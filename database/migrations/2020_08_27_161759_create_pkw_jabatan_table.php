<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePkwJabatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkw_jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bagian');
            $table->string('kode_jabatan', 40);
            $table->string('nama_jabatan', 50);
            $table->integer('tunjangan')->default(0);
            $table->timestamps();
            $table->foreign('id_bagian')->references('id')->on('pkw_bagian');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pkw_jabatan');
    }
}
