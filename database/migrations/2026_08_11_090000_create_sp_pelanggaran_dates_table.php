<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpPelanggaranDatesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sp_pelanggaran_dates')) {
            Schema::create('sp_pelanggaran_dates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('sp_pelanggaran_id')->index();
                $table->date('tanggal');
                $table->string('keterangan', 255)->nullable()->comment('Catatan per tanggal, misal: Terlambat 30 menit, Shift 1, dsb.');
                $table->timestamps();

                $table->index(['sp_pelanggaran_id', 'tanggal']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sp_pelanggaran_dates');
    }
}
