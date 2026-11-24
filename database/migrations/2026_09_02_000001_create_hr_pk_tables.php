<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrPkTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_pk')) {
            Schema::create('hr_pk', function (Blueprint $table) {
                $table->id();
                $table->string('periode', 10)->nullable();
                $table->string('file_name', 255)->nullable();
                $table->string('send_by_username', 100)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hr_pk_detail')) {
            Schema::create('hr_pk_detail', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hr_pk_id');
                $table->string('section', 150);
                $table->string('category', 100)->nullable();
                $table->string('level_group', 10)->nullable();
                $table->string('no_urut', 10);
                $table->text('area')->nullable();
                $table->text('competency_behavior')->nullable();
                $table->text('key_behavior')->nullable();
                $table->text('kriteria_1')->nullable();
                $table->text('kriteria_2')->nullable();
                $table->text('kriteria_3')->nullable();
                $table->text('kriteria_4')->nullable();
                $table->text('kriteria_5')->nullable();
                $table->timestamps();

                $table->foreign('hr_pk_id')->references('id')->on('hr_pk')->onDelete('cascade');
                $table->index(['hr_pk_id', 'no_urut'], 'hr_pk_detail_pk_no_idx');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('hr_pk_detail')) {
            Schema::dropIfExists('hr_pk_detail');
        }
        if (Schema::hasTable('hr_pk')) {
            Schema::dropIfExists('hr_pk');
        }
    }
}