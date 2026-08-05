<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrIzinTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hr_izin')) {
            return;
        }

        Schema::create('hr_izin', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 50);
            $table->string('nama', 150);
            $table->string('dept', 100)->nullable();
            $table->string('section', 100)->nullable();
            $table->date('tgl');
            $table->string('no_spi', 100)->nullable();
            $table->string('kode_ijin', 20);
            $table->text('keterangan')->nullable();
            $table->string('send_by_username', 100)->nullable();
            $table->timestamps();

            $table->index('nik', 'hr_izin_nik_index');
            $table->index('tgl', 'hr_izin_tgl_index');
            $table->index('no_spi', 'hr_izin_no_spi_index');
            $table->unique(['nik', 'tgl', 'no_spi'], 'hr_izin_nik_tgl_no_spi_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_izin');
    }
}
