<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrPmsLockingPkTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_pms_locking_pk')) {
            Schema::create('hr_pms_locking_pk', function (Blueprint $table) {
                $table->id();
                $table->string('periode', 10);
                $table->boolean('lock_input_karyawan')->default(false);
                $table->boolean('lock_input_atasan')->default(false);
                $table->boolean('lock_approval_am')->default(false);
                $table->boolean('lock_approval_manager')->default(false);
                $table->boolean('lock_unapproval')->default(false);
                $table->string('locked_by', 100)->nullable();
                $table->timestamp('locked_at')->nullable();
                $table->string('updated_by', 100)->nullable();
                $table->timestamps();

                $table->unique('periode', 'hr_pms_locking_pk_periode_uq');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('hr_pms_locking_pk')) {
            Schema::dropIfExists('hr_pms_locking_pk');
        }
    }
}
