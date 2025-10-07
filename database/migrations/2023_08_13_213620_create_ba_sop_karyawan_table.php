<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaSopKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ba_sop_karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 70);
            $table->string('nik', 20);
            $table->string('jabatan', 40);
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $table->enum('shift', ['1', '2', '3']);
            $table->string('nama_pembuat', 40);
            $table->string('jabatan_pembuat', 40);
            $table->string('nama_area', 40);
            $table->string('barang', 40);
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
        Schema::dropIfExists('ba_sop_karyawan');
    }
}
