<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrIppBuktiTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_ipp_bukti')) {
            Schema::create('hr_ipp_bukti', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ipp_otorisasi_id');
                $table->unsignedBigInteger('ipp_detail_id');
                $table->string('tahapan', 20);
                $table->string('file_path');
                $table->string('original_name');
                $table->string('mime_type', 100)->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->string('uploaded_by', 100)->nullable();
                $table->timestamps();

                $table->foreign('ipp_otorisasi_id')->references('id')->on('hr_ipp_otorisasi')->onDelete('cascade');
                $table->foreign('ipp_detail_id')->references('id')->on('hr_ipp_detail')->onDelete('cascade');
                $table->index(['ipp_otorisasi_id', 'ipp_detail_id', 'tahapan'], 'hr_ipp_bukti_oto_detail_tahapan_idx');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('hr_ipp_bukti');
    }
}
