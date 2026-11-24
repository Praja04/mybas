<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrPkPesertaTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_pk_peserta')) {
            Schema::create('hr_pk_peserta', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hr_pk_id');
                $table->string('nik', 50);
                $table->integer('jumlah_bawahan')->nullable();
                $table->timestamps();

                $table->unique(['hr_pk_id', 'nik'], 'hr_pk_peserta_pk_nik_uq');
                $table->foreign('hr_pk_id')->references('id')->on('hr_pk')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('hr_pk_peserta')) {
            Schema::dropIfExists('hr_pk_peserta');
        }
    }
}
