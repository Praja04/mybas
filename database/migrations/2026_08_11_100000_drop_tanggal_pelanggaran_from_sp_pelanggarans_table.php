<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTanggalPelanggaranFromSpPelanggaransTable extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('sp_pelanggarans', 'tanggal_pelanggaran')) {
            Schema::table('sp_pelanggarans', function (Blueprint $table) {
                $table->dropColumn('tanggal_pelanggaran');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasColumn('sp_pelanggarans', 'tanggal_pelanggaran')) {
            Schema::table('sp_pelanggarans', function (Blueprint $table) {
                $table->date('tanggal_pelanggaran')->nullable();
            });
        }
    }
}
