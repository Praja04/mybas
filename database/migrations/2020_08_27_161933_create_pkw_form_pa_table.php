<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePkwFormPaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkw_form_pa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pkw');
            $table->date('tanggal_create');
            $table->date('tanggal_penilaian')->nullable();
            $table->string('fungsi_penilaian', 20)->nullable(); // masa percobaan, masa akting, pkwt
            $table->text('evaluasi_keseluruhan')->nullable();
            $table->string('kesimpulan', 30)->nullable(); // lulus, lulus dengan catatan, tidak lulus
            $table->string('status', 10); // create, approve1, approve2
            $table->string('nama_supervisor', 150)->nullable();
            $table->enum('supervisor_approve', ['Y', 'N'])->nullable(); // Y, N
            $table->dateTime('supervisor_approve_time')->nullable();
            $table->string('nama_manager', 150)->nullable();
            $table->enum('manager_approve', ['Y','N'])->nullable(); // Y, N
            $table->dateTime('manager_approve_time')->nullable();
            $table->string('nama_fm', 150)->nullable();
            $table->enum('fm_approve', ['Y','N'])->nullable(); // Y, N
            $table->dateTime('fm_approve_time')->nullable();
            $table->timestamps();
            $table->foreign('id_pkw')->references('id')->on('pkw');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pkw_form_pa');
    }
}
