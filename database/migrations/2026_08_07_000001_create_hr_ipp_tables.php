<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrIppTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_ipp')) {
            Schema::create('hr_ipp', function (Blueprint $table) {
                $table->id();
                $table->string('periode', 50);
                $table->string('dept', 150)->nullable();
                $table->string('position', 200)->nullable();
                $table->string('golongan', 50)->nullable();
                $table->string('send_by_username', 100)->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('hr_ipp') && !Schema::hasColumn('hr_ipp', 'template_type')) {
            Schema::table('hr_ipp', function (Blueprint $table) {
                $table->string('template_type', 20)->default('kpi')->after('golongan');
            });
        }

        if (!Schema::hasTable('hr_ipp_detail')) {
            Schema::create('hr_ipp_detail', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ipp_id');
                $table->integer('no_urut');
                $table->string('objective', 255)->nullable();
                $table->string('kpa', 255)->nullable();
                $table->string('kpi', 255);
                $table->string('classification', 100)->nullable();
                $table->string('uom', 100)->nullable();
                $table->decimal('weight', 12, 2)->nullable();
                $table->string('target', 255)->nullable();
                $table->string('due_date', 100)->nullable();
                $table->timestamps();

                $table->foreign('ipp_id')->references('id')->on('hr_ipp')->onDelete('cascade');
                $table->index(['ipp_id', 'no_urut'], 'hr_ipp_detail_ipp_no_idx');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('hr_ipp') && Schema::hasColumn('hr_ipp', 'template_type')) {
            Schema::table('hr_ipp', function (Blueprint $table) {
                $table->dropColumn('template_type');
            });
        }
        if (Schema::hasTable('hr_ipp_target')) {
            Schema::dropIfExists('hr_ipp_target');
        }
        if (Schema::hasTable('hr_ipp_detail')) {
            Schema::dropIfExists('hr_ipp_detail');
        }
        if (Schema::hasTable('hr_ipp')) {
            Schema::dropIfExists('hr_ipp');
        }
    }
}
