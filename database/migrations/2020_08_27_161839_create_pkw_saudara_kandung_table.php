<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePkwSaudaraKandungTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkw_saudara_kandung', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan');
            $table->string('nama', 150);
            $table->string('jenis_kelamin', 10);
            $table->string('tempat_lahir', 150)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->timestamps();
            $table->foreign('id_karyawan')->references('id')->on('karyawan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pkw_saudara_kandung');
    }
}
