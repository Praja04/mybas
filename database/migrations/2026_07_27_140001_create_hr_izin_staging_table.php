<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrIzinStagingTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hr_izin_staging')) {
            return;
        }

        Schema::create('hr_izin_staging', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 50);
            $table->string('nama', 150);
            $table->string('dept', 100)->nullable();
            $table->string('section', 100)->nullable();
            $table->date('tgl')->nullable();
            $table->string('no_spi', 100)->nullable();
            $table->string('kode_ijin', 20)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('send_by_username', 100)->nullable();
            $table->string('batch_id', 50)->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamps();

            $table->index('nik', 'hr_izin_staging_nik_index');
            $table->index('tgl', 'hr_izin_staging_tgl_index');
            $table->index('no_spi', 'hr_izin_staging_no_spi_index');
            $table->index('batch_id', 'hr_izin_staging_batch_id_index');
            $table->index('status', 'hr_izin_staging_status_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_izin_staging');
    }
}
