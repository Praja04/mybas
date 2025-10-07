<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePkwKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkw_karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->nullable();
            $table->string('nama', 150);
            $table->string('agama', 50);
            $table->string('jenis_kelamin', 20);
            $table->string('tempat_lahir', 50);
            $table->date('tanggal_lahir');
            $table->date('tanggal_masuk');
            $table->string('status_perdata', 15);
            $table->string('nama_pasangan', 15)->nullable();
            $table->string('tempat_pernikahan', 150)->nullable();
            $table->date('tanggal_pernikahan')->nullable();
            $table->string('tempat_lahir_pasangan', 150)->nullable();
            $table->date('tanggal_lahir_pasangan')->nullable();
            $table->string('pekerjaan_pasangan', 150)->nullable();
            $table->string('tempat_pasangan_bekerja', 150)->nullable();
            $table->string('nama_ayah', 150);
            $table->string('tempat_lahir_ayah', 150)->nullable();
            $table->date('tanggal_lahir_ayah')->nullable();
            $table->string('nama_ibu', 150);
            $table->string('tempat_lahir_ibu', 150)->nullable();
            $table->date('tanggal_lahir_ibu')->nullable();
            $table->string('nama_ayah_mertua', 150)->nullable();
            $table->string('tempat_lahir_ayah_mertua', 150)->nullable();
            $table->date('tanggal_lahir_ayah_mertua')->nullable();
            $table->string('nama_ibu_mertua', 150)->nullable();
            $table->string('tempat_lahir_ibu_mertua', 150)->nullable();
            $table->date('tanggal_lahir_ibu_mertua')->nullable();
            $table->string('nama_kontak_darurat', 150);
            $table->string('hubungan_kontak_darurat', 150);
            $table->string('no_telepon_kontak_darurat', 20);
            $table->string('nomor_rekening_bank', 50)->nullable();
            $table->string('nomor_kartu_bpjs_ketenagakerjaan', 50)->nullable();
            $table->string('keterangan_kartu_bpjs_ketenagakerjaan', 20);
            $table->string('nomor_kartu_bpjs_kesehatan', 50)->nullable();
            $table->string('keterangan_kartu_bpjs_kesehatan', 20);
            $table->foreignId('id_divisi')->nullable();
            $table->foreignId('id_bagian')->nullable();
            $table->foreignId('id_group')->nullable();
            $table->foreignId('id_jabatan')->nullable();
            $table->timestamps();
            $table->foreign('id_divisi')->references('id')->on('pkw_divisi');
            $table->foreign('id_bagian')->references('id')->on('pkw_bagian');
            $table->foreign('id_group')->references('id')->on('pkw_group');
            $table->foreign('id_jabatan')->references('id')->on('pkw_jabatan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pkw_karyawan');
    }
}
