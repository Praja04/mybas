<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPwsToHrMasterEmployeeTables extends Migration
{
    public function up()
    {
        Schema::table('hr_master_employee', function (Blueprint $table) {
            $table->string('PWS')->nullable()->after('NIK');
        });

        Schema::table('hr_master_employee_staging', function (Blueprint $table) {
            $table->string('PWS')->nullable()->after('NIK');
        });
    }

    public function down()
    {
        Schema::table('hr_master_employee', function (Blueprint $table) {
            $table->dropColumn('PWS');
        });

        Schema::table('hr_master_employee_staging', function (Blueprint $table) {
            $table->dropColumn('PWS');
        });
    }
}
