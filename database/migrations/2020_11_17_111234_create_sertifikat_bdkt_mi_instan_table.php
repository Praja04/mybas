<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSertifikatBdktMiInstanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sigra_sertifikat_bdkt_mi_instan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_varian');
            $table->string('transaction_id', 100);
            $table->string('nomor_sertifikat', 50);
            $table->date('tanggal_terbit');
            $table->date('tanggal_expired')->nullable();
            $table->string('masa_berlaku', 20);
            $table->text('keterangan')->nullable();
            $table->string('status', 20);
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
        Schema::dropIfExists('sigra_sertifikat_bdkt_mi_instan');
    }
}
