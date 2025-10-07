<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaksiKejadianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saksi_kejadian', function (Blueprint $table) {
            $table->id();
            $table->string('lk_id');
            $table->string('nama_saksi', 70);
            $table->string('nik_saksi', 15);
            $table->string('departement_saksi', 50);
            $table->string('keterangan_saksi', 50);
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
        Schema::dropIfExists('saksi_kejadian');
    }
}
