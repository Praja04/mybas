<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMangkirColumnsToSpPelanggarans extends Migration
{
    public function up()
    {
        Schema::table('sp_pelanggarans', function (Blueprint $table) {
            if (!Schema::hasColumn('sp_pelanggarans', 'sumber_data')) {
                $table->string('sumber_data')->default('PELANGGARAN')->after('employee_id');
            }
            if (!Schema::hasColumn('sp_pelanggarans', 'mangkir_ke')) {
                $table->integer('mangkir_ke')->nullable()->after('kode_ir');
            }
            if (!Schema::hasColumn('sp_pelanggarans', 'bulan_mangkir')) {
                $table->string('bulan_mangkir', 7)->nullable()->after('mangkir_ke');
            }
        });
    }

    public function down()
    {
        Schema::table('sp_pelanggarans', function (Blueprint $table) {
            if (Schema::hasColumn('sp_pelanggarans', 'sumber_data')) {
                $table->dropColumn('sumber_data');
            }
            if (Schema::hasColumn('sp_pelanggarans', 'mangkir_ke')) {
                $table->dropColumn('mangkir_ke');
            }
            if (Schema::hasColumn('sp_pelanggarans', 'bulan_mangkir')) {
                $table->dropColumn('bulan_mangkir');
            }
        });
    }
}
