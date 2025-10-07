<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePkwAnakAnakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkw_anak_anak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan');
            $table->string('nama', 150);
            $table->string('jenis_kelamin', 10)->nullable();
            $table->string('tempat_lahir', 150)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->timestamps();
            $table->foreign('id_karyawan')->references('id')->on('pkw_karyawan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pkw_anak_anak');
    }
}
