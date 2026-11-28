<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKategoriKodeToSpKodePelanggarans extends Migration
{
    public function up()
    {
        // Drop unique index if exists & widen kode column
        try {
            Schema::table('sp_kode_pelanggarans', function (Blueprint $table) {
                $table->dropUnique('sp_kode_pelanggarans_kode_unique');
            });
        } catch (\Exception $e) {
            // Index might not exist or already dropped
        }

        \DB::statement("ALTER TABLE `sp_kode_pelanggarans` MODIFY `kode` VARCHAR(255) NOT NULL");

        Schema::table('sp_kode_pelanggarans', function (Blueprint $table) {
            if (!Schema::hasColumn('sp_kode_pelanggarans', 'kategori_kode')) {
                $table->string('kategori_kode', 20)->default('ADMIN')->after('kode');
            }
            if (!Schema::hasColumn('sp_kode_pelanggarans', 'bentuk_pelanggaran')) {
                $table->text('bentuk_pelanggaran')->nullable()->after('nama_pelanggaran');
            }
            if (!Schema::hasColumn('sp_kode_pelanggarans', 'dasar_pertimbangan')) {
                $table->text('dasar_pertimbangan')->nullable()->after('pasal_dilanggar');
            }
        });
    }

    public function down()
    {
        Schema::table('sp_kode_pelanggarans', function (Blueprint $table) {
            if (Schema::hasColumn('sp_kode_pelanggarans', 'kategori_kode')) {
                $table->dropColumn('kategori_kode');
            }
            if (Schema::hasColumn('sp_kode_pelanggarans', 'bentuk_pelanggaran')) {
                $table->dropColumn('bentuk_pelanggaran');
            }
            if (Schema::hasColumn('sp_kode_pelanggarans', 'dasar_pertimbangan')) {
                $table->dropColumn('dasar_pertimbangan');
            }
        });
    }
}
