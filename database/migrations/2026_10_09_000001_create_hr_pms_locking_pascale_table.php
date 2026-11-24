<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrPmsLockingPascaleTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_pms_locking_pascale')) {
            Schema::create('hr_pms_locking_pascale', function (Blueprint $table) {
                $table->id();
                $table->string('periode', 10);
                $table->boolean('is_locked')->default(false);
                $table->string('locked_by', 100)->nullable();
                $table->timestamp('locked_at')->nullable();
                $table->string('updated_by', 100)->nullable();
                $table->timestamps();

                $table->unique('periode', 'hr_pms_locking_pascale_periode_uq');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('hr_pms_locking_pascale')) {
            Schema::dropIfExists('hr_pms_locking_pascale');
        }
    }
}
