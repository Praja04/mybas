<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrPmsNormalScaleTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_pms_normal_scale')) {
            Schema::create('hr_pms_normal_scale', function (Blueprint $table) {
                $table->id();
                $table->string('periode', 10);
                $table->string('level_group', 10)->default('4-7');
                $table->decimal('grade_a', 5, 2)->default(5);
                $table->decimal('grade_b', 5, 2)->default(15);
                $table->decimal('grade_c', 5, 2)->default(65);
                $table->decimal('grade_d', 5, 2)->default(10);
                $table->decimal('grade_e', 5, 2)->default(5);
                $table->unsignedSmallInteger('minimum_peserta')->default(3);
                $table->string('updated_by', 100)->nullable();
                $table->timestamps();

                $table->unique(['periode', 'level_group'], 'hr_pms_normal_scale_periode_group_uq');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('hr_pms_normal_scale')) {
            Schema::dropIfExists('hr_pms_normal_scale');
        }
    }
}
