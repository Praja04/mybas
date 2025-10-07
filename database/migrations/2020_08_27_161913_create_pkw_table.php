<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePkwTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkw', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan');
            $table->string('kontrak_ke', 150); // percobaan ke 1 atau percobaan ke dua
            $table->date('tanggal_phk')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('keterangan', 150)->nullable();
            $table->enum('jenis', ['pkwtt', 'pkwt'])->default('pkwtt');
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
        Schema::dropIfExists('pkw');
    }
}
