<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaLaporanKejadianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ba_laporan_kejadian', function (Blueprint $table) {
            $table->string('lk_id')->primary();
            $table->enum('jenis_kejadian', ['kecelakaan lalu lintas', 'penemuan barang', 'kecelakaan kerja', 'pencurian', 'perkelahian', 'tindak kekerasan', 'kebakaran', 'demonstrasi', 'tindakan asusila', 'pengerusakan', 'tindakan indispliner']);
            $table->string('nama_korban', 70);
            $table->string('nik_korban', 70);
            $table->string('perusahaan_korban', 70);
            $table->string('bagian_korban', 70);
            $table->text('lokasi_kejadian');
            // $table->bigInteger('fk_id')->unsigned()->nullable();
            $table->text('yang_terjadi');
            $table->string('nama_terlapor', 70);
            $table->string('umur_terlapor', 20);
            $table->string('ttl_terlapor', 50);
            $table->string('pekerjaan_terlapor', 50);
            $table->string('alamat_terlapor', 50);
            $table->string('kelurahan_terlapor', 50);
            $table->string('kecamatan_terlapor', 50);
            $table->string('provinsi_terlapor', 50);
            $table->enum('status_terlapor', ['sudah kawin', 'belum kawin', 'janda/duda']);
            $table->string('agama_terlapor', 20);
            $table->string('kebangsaan_terlapor', 50);
            $table->string('no_ktp_terlapor', 50);
            $table->string('no_simc_terlapor', 50);
            $table->string('no_hp_terlapor', 50);
            $table->text('bagaimana_terjadi');
            $table->text('mengapa_terjadi');
            // $table->bigInteger('sk_id')->unsigned()->nullable();
            $table->text('uraian_kejadian');
            $table->text('tindakan_pengamanan');
            $table->text('hasil_daritindakan');
            $table->text('saran');
            // $table->bigInteger('dk_id')->unsigned()->nullable();
            $table->timestamps();
            $table->dateTime('deleted_at');
            // $table->foreign('fk_id')->references('id')->on('fakta_kejadian')->onDelete('cascade');
            // $table->foreign('sk_id')->references('id')->on('saksi_kejadian')->onDelete('cascade');
            // $table->foreign('dk_id')->references('id')->on('dokumentasi_kejadian')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ba_laporan_kejadian');
    }
}
