<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataTamuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_data_tamu', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('nama_instansi', 200)->nullable();
            $table->string('jenis_kunjungan', 150)->nullable();
            $table->string('no_identitas', 20);
            $table->date('tanggal');
            $table->string('bertemu_dengan', 150);
            $table->enum('jawaban_pertanyaan_1', ['Ya','Tidak']);
            $table->enum('jawaban_pertanyaan_2', ['Ya','Tidak']);
            $table->enum('jawaban_pertanyaan_3', ['Ya','Tidak']);
            $table->string('keterangan_pertanyaan_3', 250)->nullable();
            $table->enum('jawaban_pertanyaan_4', ['Ya','Tidak']);
            $table->enum('jawaban_pertanyaan_5', ['Ya','Tidak']);
            $table->enum('jawaban_pertanyaan_6', ['Ya','Tidak']);
            $table->string('jawaban_pertanyaan_7', 10);
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
        Schema::dropIfExists('hr_data_tamu');
    }
}
