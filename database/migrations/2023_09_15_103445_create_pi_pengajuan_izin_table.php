<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePiPengajuanIzinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pi_pengajuan_izin', function (Blueprint $table) {
            $table->string('id_pengajuan')->primary();
            $table->string('id_jenis');
            $table->string('nik_karyawan');
            $table->string('id_dept_head');
            $table->dateTime('dept_head_approved_at')->nullable();
            $table->string('id_gm')->nullable();
            $table->dateTime('gm_approved_at')->nullable();
            $table->text('alasan_ditolak')->nullable();
            $table->char('status', '1')->default('1');
            $table->timestamps();
            $table->foreign('id_jenis')->references('id_jenis')->on('pi_jenis')->onDelete('cascade');
            // $table->foreign('nik_karyawan')->references('nik')->on('hr_karyawan')->onDelete('cascade');
            $table->foreign('id_dept_head')->references('id_dept_head')->on('pi_dept_head')->onDelete('cascade');
            $table->foreign('id_gm')->references('id_gm')->on('pi_gm')->onDelete('cascade');
            $table->text('alasan_takeout')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pi_pengajuan_izin');
    }
}
