<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengirimCateringbasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengirim_cateringbas', function (Blueprint $table) {
            $table->id();
            $table->string('id_transaksi')->nullable();
            $table->string('foto')->nullable();
            $table->date('tanggal');
            $table->string('catering');
            $table->enum('shift', [1, 2, 3]);
            $table->enum('status_cek_kendaraan', ['sudah', 'belum'])->default('belum');
            $table->enum('status_cek_kedatangan', ['sudah', 'belum'])->default('belum');
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
        Schema::dropIfExists('pengirim_cateringbas');
    }
}
