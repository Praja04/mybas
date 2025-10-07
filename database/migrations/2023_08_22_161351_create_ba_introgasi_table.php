<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaIntrogasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ba_introgasi', function (Blueprint $table) {
            $table->string('bai_id')->primary();
            $table->enum('jenis_kejadian', ['kecelakaan lalu lintas', 'penemuan barang', 'kecelakaan kerja', 'pencurian', 'perkelahian', 'tindak kekerasan', 'kebakaran', 'demonstrasi', 'tindakan asusila', 'pengerusakan', 'tindakan indispliner']);
            $table->string('nama_introgasi');
            $table->string('umur_introgasi');
            $table->string('pekerjaan_introgasi');
            $table->string('bagian_introgasi');
            $table->string('nama_pelapor');
            $table->string('detail_barang_kejadian');
            $table->string('tempat_kejadian');
            $table->string('nama_korban');
            $table->string('nik_korban');
            $table->string('bagian_korban');
            $table->string('nama_pelaku');
            $table->string('umur_pelaku');
            $table->string('ttl_pelaku');
            $table->string('pekerjaan_pelaku');
            $table->string('nik_pelaku');
            $table->string('bagian_pelaku');
            $table->text('alamat_pelaku');
            $table->string('agama_pelaku');
            $table->string('suku_pelaku');
            $table->enum('status_pelaku', ['sudah kawin', 'belum kawin', 'janda/duda']);
            $table->enum('shift', ['1', '2', '3']);
            $table->string('pendidikan_pelaku');
            $table->string('nik_ktp_pelaku');
            $table->string('no_hp_pelaku');
            $table->string('tempat_introgasi');
            $table->string('keterangan_kejadian');
            $table->string('image');
            $table->integer('item_id');
            $table->timestamps();
            $table->dateTime('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ba_introgasi');
    }
}
