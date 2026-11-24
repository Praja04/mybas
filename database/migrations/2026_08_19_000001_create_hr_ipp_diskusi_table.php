<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrIppDiskusiTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_ipp_diskusi')) {
            Schema::create('hr_ipp_diskusi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ipp_otorisasi_id');
                $table->unsignedBigInteger('ipp_detail_id')->nullable();
                $table->string('tahapan', 20)->nullable();
                $table->string('nik_pengaju', 50);
                $table->string('nama_pengaju', 150);
                $table->text('pesan');
                $table->text('jawaban')->nullable();
                $table->string('nik_penjawab', 50)->nullable();
                $table->string('nama_penjawab', 150)->nullable();
                $table->string('status', 20)->default('open');
                $table->timestamp('answered_at')->nullable();
                $table->timestamps();

                $table->foreign('ipp_otorisasi_id')->references('id')->on('hr_ipp_otorisasi')->onDelete('cascade');
                $table->foreign('ipp_detail_id')->references('id')->on('hr_ipp_detail')->onDelete('cascade');
                $table->index(['ipp_otorisasi_id', 'ipp_detail_id'], 'hr_ipp_diskusi_oto_detail_idx');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('hr_ipp_diskusi');
    }
}
