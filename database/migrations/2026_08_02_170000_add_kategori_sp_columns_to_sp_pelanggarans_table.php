<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKategoriSpColumnsToSpPelanggaransTable extends Migration
{
    public function up()
    {
        Schema::table('sp_pelanggarans', function (Blueprint $table) {
            if (!Schema::hasColumn('sp_pelanggarans', 'kategori_sp')) {
                $table->string('kategori_sp', 50)->default('PROSES')->nullable()->after('current_status');
            }
            if (!Schema::hasColumn('sp_pelanggarans', 'masa_berlaku_sampai')) {
                $table->date('masa_berlaku_sampai')->nullable()->after('kategori_sp');
            }
            if (!Schema::hasColumn('sp_pelanggarans', 'is_active')) {
                $table->boolean('is_active')->default(false)->after('masa_berlaku_sampai');
            }
        });
    }

    public function down()
    {
        Schema::table('sp_pelanggarans', function (Blueprint $table) {
            $table->dropColumn(['kategori_sp', 'masa_berlaku_sampai', 'is_active']);
        });
    }
}
