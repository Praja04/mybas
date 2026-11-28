<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLevelToHrMasterEmployeeTables extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('hr_master_employee', 'Level')) {
            Schema::table('hr_master_employee', function (Blueprint $table) {
                $table->string('Level')->nullable()->after('PWS');
            });
        }

        if (!Schema::hasColumn('hr_master_employee_staging', 'Level')) {
            Schema::table('hr_master_employee_staging', function (Blueprint $table) {
                $table->string('Level')->nullable()->after('PWS');
            });
        }
    }

    public function down()
    {
        Schema::table('hr_master_employee', function (Blueprint $table) {
            $table->dropColumn('Level');
        });

        Schema::table('hr_master_employee_staging', function (Blueprint $table) {
            $table->dropColumn('Level');
        });
    }
}