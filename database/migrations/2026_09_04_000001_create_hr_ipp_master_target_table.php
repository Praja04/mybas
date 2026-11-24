<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrIppMasterTargetTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_ipp_master_target')) {
            Schema::create('hr_ipp_master_target', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ipp_detail_id');
                $table->unsignedTinyInteger('score');
                $table->string('match_type', 10)->default('range');
                $table->decimal('min_value', 18, 6)->nullable();
                $table->decimal('max_value', 18, 6)->nullable();
                $table->string('label', 100)->nullable();
                $table->timestamps();

                $table->foreign('ipp_detail_id')->references('id')->on('hr_ipp_detail')->onDelete('cascade');
                $table->unique(['ipp_detail_id', 'score'], 'hr_ipp_master_target_detail_score_uq');
                $table->index('ipp_detail_id', 'hr_ipp_master_target_detail_idx');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('hr_ipp_master_target')) {
            Schema::dropIfExists('hr_ipp_master_target');
        }
    }
}
