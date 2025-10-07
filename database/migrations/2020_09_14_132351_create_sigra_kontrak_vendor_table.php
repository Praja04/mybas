<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSigraKontrakVendorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sigra_kontrak_vendor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_vendor');
            $table->foreignId('id_perusahaan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('value')->nullable();
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
        Schema::dropIfExists('sigra_kontrak_vendor');
    }
}
