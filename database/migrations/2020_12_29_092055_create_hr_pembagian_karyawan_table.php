<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrPembagianKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_pembagian_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pembagian');
            $table->string('nik', 15);
            $table->string('nama', 150);
            $table->string('department', 50);
            $table->string('status_ambil', 50)->default('belum'); // sudah / belum
            $table->datetime('waktu_ambil')->nullable();
            $table->string('pic', 150)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('created_by', 15);
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
        Schema::dropIfExists('hr_pembagian_karyawan');
    }
}
