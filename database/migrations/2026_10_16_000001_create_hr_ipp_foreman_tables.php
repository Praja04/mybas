<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrIppForemanTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_ipp_foreman')) {
            Schema::create('hr_ipp_foreman', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ipp_id');
                $table->string('no_urut', 10);
                $table->string('section', 150)->nullable();
                $table->text('area')->nullable();
                $table->text('competency_behavior')->nullable();
                $table->text('key_behavior')->nullable();
                $table->text('kriteria_1')->nullable();
                $table->text('kriteria_2')->nullable();
                $table->text('kriteria_3')->nullable();
                $table->text('kriteria_4')->nullable();
                $table->text('kriteria_5')->nullable();
                $table->timestamps();

                $table->foreign('ipp_id')->references('id')->on('hr_ipp')->onDelete('cascade');
                $table->index(['ipp_id', 'no_urut'], 'hr_ipp_foreman_ipp_no_idx');
            });
        }

        if (!Schema::hasTable('hr_ipp_foreman_nilai')) {
            Schema::create('hr_ipp_foreman_nilai', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hr_ipp_foreman_id');
                $table->string('nik', 50);
                $table->string('nama', 150)->nullable();
                $table->string('tahapan', 20);
                $table->tinyInteger('nilai')->nullable();
                $table->string('filled_by', 100)->nullable();
                $table->timestamp('filled_at')->nullable();
                $table->string('approval_associate_manager', 20)->nullable();
                $table->string('approval_by_associate_manager', 100)->nullable();
                $table->timestamp('approval_at_associate_manager')->nullable();
                $table->text('note_associate_manager')->nullable();
                $table->string('approval_manager', 20)->nullable();
                $table->string('approval_by_manager', 100)->nullable();
                $table->timestamp('approval_at_manager')->nullable();
                $table->text('note_manager')->nullable();
                $table->timestamps();

                $table->foreign('hr_ipp_foreman_id')->references('id')->on('hr_ipp_foreman')->onDelete('cascade');
                $table->unique(['hr_ipp_foreman_id', 'nik', 'tahapan'], 'hr_ipp_foreman_nilai_row_nik_tahapan_uq');
                $table->index(['nik', 'tahapan'], 'hr_ipp_foreman_nilai_nik_tahapan_idx');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('hr_ipp_foreman_nilai');
        Schema::dropIfExists('hr_ipp_foreman');
    }
}
