<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 12)->unique();
            $table->string('nama', 50);
            $table->string('agama', 50);
            $table->string('jenis_kelamin', 1);
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
            $table->string('nik_ktp', 16)->nullable();
            $table->string('no_kk', 20)->nullable();
            $table->string('no_npwp', 50)->nullable();
            $table->char('alamat_ktp_provinsi', 2)->nullable();
            $table->char('alamat_ktp_kota', 4)->nullable();
            $table->char('alamat_ktp_kecamatan', 7)->nullable();
            $table->char('alamat_ktp_desa', 10)->nullable();
            $table->text('alamat_ktp')->nullable();
            $table->char('alamat_sekarang_provinsi', 2)->nullable();
            $table->char('alamat_sekarang_kota', 4)->nullable();
            $table->char('alamat_sekarang_kecamatan', 7)->nullable();
            $table->char('alamat_sekarang_desa', 10)->nullable();
            $table->text('alamat_sekarang')->nullable();
            $table->text('foto_diri')->nullable();
            $table->text('foto_ktp')->nullable();
            $table->text('foto_npwp')->nullable();
            $table->text('foto_kk')->nullable();
            $table->string('level', 5)->default(1);
            $table->string('pendidikan', 30)->nullable();
            $table->string('nomor_hp', 30)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('sosmed_facebook', 255)->nullable();
            $table->string('sosmed_twitter', 255)->nullable();
            $table->string('sosmed_linkedin', 255)->nullable();
            $table->string('sosmed_instagram', 255)->nullable();
            $table->string('nama_sekolah', 255)->nullable();
            $table->string('jurusan', 255)->nullable();
            $table->string('kursus', 255)->nullable();
            $table->string('golongan_darah', 5)->nullable();
            $table->string('kode_divisi', 50);
            $table->string('kode_bagian', 50);
            $table->string('kode_group', 50);
            $table->string('kode_jabatan', 50);
            $table->string('kode_admin', 50);
            $table->string('kode_periode', 50);
            $table->string('kode_kontrak', 50);
            $table->string('status_pph21', 50);
            $table->string('journal_group', 50);
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
        Schema::dropIfExists('hr_karyawan');
    }
}
