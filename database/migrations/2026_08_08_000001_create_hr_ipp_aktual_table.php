<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateHrIppAktualTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_ipp_aktual')) {
            Schema::create('hr_ipp_aktual', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ipp_otorisasi_id');
                $table->unsignedBigInteger('ipp_detail_id');
                $table->string('tahapan', 20);
                $table->decimal('aktual', 20, 10)->nullable();
                $table->string('aktual_varchar', 100)->nullable();
                $table->decimal('aktual_atasan', 20, 10)->nullable();
                $table->string('aktual_atasan_varchar', 100)->nullable();
                $table->string('aktual_atasan_by', 100)->nullable();
                $table->decimal('aktual_departemen', 20, 10)->nullable();
                $table->string('aktual_departemen_varchar', 100)->nullable();
                $table->string('aktual_departemen_by', 100)->nullable();
                $table->string('achievement_category', 20)->nullable();
                $table->decimal('achievement_score', 12, 4)->nullable();
                $table->decimal('score_scale', 3, 1)->nullable();
                $table->boolean('review')->default(false);
                $table->string('reviewed_by', 100)->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->string('keterangan', 255)->nullable();
                $table->string('send_by_username', 100)->nullable();
                $table->string('approval_associate_manager', 20)->nullable();
                $table->string('approval_by_associate_manager', 100)->nullable();
                $table->timestamp('approval_at_associate_manager')->nullable();
                $table->string('approval_manager', 20)->nullable();
                $table->string('approval_by_manager', 100)->nullable();
                $table->timestamp('approval_at_manager')->nullable();
                $table->timestamps();

                $table->foreign('ipp_otorisasi_id')->references('id')->on('hr_ipp_otorisasi')->onDelete('cascade');
                $table->foreign('ipp_detail_id')->references('id')->on('hr_ipp_detail')->onDelete('cascade');
                $table->unique(['ipp_otorisasi_id', 'ipp_detail_id', 'tahapan'], 'hr_ipp_aktual_oto_detail_tahapan_unique');
            });

            return;
        }

        DB::statement("
            ALTER TABLE `hr_ipp_aktual`
                MODIFY `aktual` DECIMAL(20,10) NULL,
                MODIFY `aktual_atasan` DECIMAL(20,10) NULL,
                MODIFY `aktual_departemen` DECIMAL(20,10) NULL
        ");
    }

    public function down()
    {
        Schema::dropIfExists('hr_ipp_aktual');
    }
}
