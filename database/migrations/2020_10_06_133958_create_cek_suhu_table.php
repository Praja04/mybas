<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCekSuhuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cek_suhu', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nik', 15);
            $table->string('nama', 255);
            $table->float('suhu');
            $table->timestamp('waktu_scan');
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
        Schema::dropIfExists('cek_suhu');
    }
}
