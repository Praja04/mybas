<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDokumentasiIntrogasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dokumentasi_introgasi', function (Blueprint $table) {
            $table->id();
            $table->string('bai_id');
            $table->string('foto_introgasi');
            $table->string('keterangan_introgasi');
            $table->timestamps();
            $table->foreign('bai_id')->references('bai_id')->on('ba_introgasi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dokumentasi_introgasi');
    }
}
