<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrPmsLockingIppTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_pms_locking_ipp')) {
            Schema::create('hr_pms_locking_ipp', function (Blueprint $table) {
                $table->id();
                $table->string('tahapan', 20);
                $table->boolean('lock_aktual_departemen')->default(false);
                $table->boolean('lock_aktual_karyawan')->default(false);
                $table->boolean('lock_aktual_atasan')->default(false);
                $table->boolean('lock_batalkan_approval')->default(false);
                $table->string('locked_by', 100)->nullable();
                $table->timestamp('locked_at')->nullable();
                $table->string('updated_by', 100)->nullable();
                $table->timestamps();

                $table->unique('tahapan', 'hr_pms_locking_ipp_tahapan_uq');
            });
        } else {
            Schema::table('hr_pms_locking_ipp', function (Blueprint $table) {
                if (!Schema::hasColumn('hr_pms_locking_ipp', 'lock_batalkan_approval')) {
                    $table->boolean('lock_batalkan_approval')->default(false)->after('lock_aktual_atasan');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('hr_pms_locking_ipp')) {
            Schema::dropIfExists('hr_pms_locking_ipp');
        }
    }
}
