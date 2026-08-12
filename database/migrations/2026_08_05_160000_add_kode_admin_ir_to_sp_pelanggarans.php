<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKodeAdminIrToSpPelanggarans extends Migration
{
    public function up()
    {
        Schema::table('sp_pelanggarans', function (Blueprint $table) {
            if (!Schema::hasColumn('sp_pelanggarans', 'kode_admin')) {
                $table->string('kode_admin')->nullable()->after('employee_id');
            }
            if (!Schema::hasColumn('sp_pelanggarans', 'kode_ir')) {
                $table->string('kode_ir')->nullable()->after('kode_admin');
            }
        });

        // Make jenis_pelanggaran nullable using DB raw statement to avoid doctrine requirement
        \DB::statement("ALTER TABLE `sp_pelanggarans` MODIFY `jenis_pelanggaran` VARCHAR(255) NULL");
    }

    public function down()
    {
        Schema::table('sp_pelanggarans', function (Blueprint $table) {
            if (Schema::hasColumn('sp_pelanggarans', 'kode_admin')) {
                $table->dropColumn('kode_admin');
            }
            if (Schema::hasColumn('sp_pelanggarans', 'kode_ir')) {
                $table->dropColumn('kode_ir');
            }
        });
    }
}
