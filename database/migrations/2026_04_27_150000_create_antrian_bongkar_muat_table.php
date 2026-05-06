<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAntrianBongkarMuatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('antrian_bongkar_muat', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_antrian'); // BAS-001
            $table->enum('status', ['waiting', 'called', 'serving', 'skipped', 'completed'])->default('waiting');
            $table->timestamp('waktu_dipanggil')->nullable();
            $table->timestamp('waktu_dilayani')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
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
        Schema::dropIfExists('antrian_bongkar_muat');
    }
}
