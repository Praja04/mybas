<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSigraSertifikasiOperasionalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sigra_sertifikasi_operasional', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_operasional');
            $table->string('perizinan', 10);
            $table->integer('harga')->nullable();
            $table->date('tanggal_sertifikasi');
            $table->date('tanggal_expired')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('dokumen_asli', 10)->nullable();
            $table->string('scan', 10)->nullable();
            $table->text('remarks')->nullable();
            $table->year('tahun')->nullable();
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
        Schema::dropIfExists('sigra_sertifikasi_operasional');
    }
}
