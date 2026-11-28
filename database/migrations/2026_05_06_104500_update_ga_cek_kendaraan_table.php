<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGaCekKendaraanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ga_cek_kendaraan', function (Blueprint $table) {
            // Rename nama_petugas to nama_petugas_masuk if it exists
            // Since we don't have Doctrine DBAL, we'll just check and add
            if (!Schema::hasColumn('ga_cek_kendaraan', 'nama_petugas_masuk')) {
                $table->string('nama_petugas_masuk')->nullable()->after('nomor_polisi');
            }
            
            if (!Schema::hasColumn('ga_cek_kendaraan', 'nama_petugas_keluar')) {
                $table->string('nama_petugas_keluar')->nullable()->after('nama_petugas_masuk');
            }
            
            if (!Schema::hasColumn('ga_cek_kendaraan', 'checked_in_at')) {
                $table->dateTime('checked_in_at')->nullable()->after('foto_in');
            }
            
            if (!Schema::hasColumn('ga_cek_kendaraan', 'checked_out_at')) {
                $table->dateTime('checked_out_at')->nullable()->after('checked_in_at');
            }
            
            if (!Schema::hasColumn('ga_cek_kendaraan', 'foto_out')) {
                $table->text('foto_out')->nullable()->after('checked_out_at');
            }
        });
        
        // Data migration: if nama_petugas has data, copy it to nama_petugas_masuk
        if (Schema::hasColumn('ga_cek_kendaraan', 'nama_petugas') && Schema::hasColumn('ga_cek_kendaraan', 'nama_petugas_masuk')) {
             DB::statement("UPDATE ga_cek_kendaraan SET nama_petugas_masuk = nama_petugas WHERE nama_petugas_masuk IS NULL AND nama_petugas IS NOT NULL");
        }
        
        // Data migration: if tgl_periksa and jam_periksa exist, sync to checked_in_at
        if (Schema::hasColumn('ga_cek_kendaraan', 'tgl_periksa') && Schema::hasColumn('ga_cek_kendaraan', 'jam_periksa')) {
             DB::statement("UPDATE ga_cek_kendaraan SET checked_in_at = CONCAT(tgl_periksa, ' ', jam_periksa) WHERE checked_in_at IS NULL AND tgl_periksa IS NOT NULL AND jam_periksa IS NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ga_cek_kendaraan', function (Blueprint $table) {
            $table->dropColumn([
                'nama_petugas_masuk',
                'nama_petugas_keluar',
                'checked_in_at',
                'checked_out_at',
                'foto_out'
            ]);
        });
    }
}
