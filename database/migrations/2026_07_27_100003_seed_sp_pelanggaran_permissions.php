<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedSpPelanggaranPermissions extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            ['name' => 'SP Pelanggaran - Menu Utama', 'codename' => 'sp_pelanggaran'],
            ['name' => 'SP Pelanggaran - Role Admin (Inputter)', 'codename' => 'sp_pelanggaran_admin'],
            ['name' => 'SP Pelanggaran - Role Dept Head', 'codename' => 'sp_pelanggaran_dh'],
            ['name' => 'SP Pelanggaran - Role IR Staff', 'codename' => 'sp_pelanggaran_ir_staff'],
            ['name' => 'SP Pelanggaran - Role IR Head', 'codename' => 'sp_pelanggaran_ir_head'],
        ];

        foreach ($permissions as $perm) {
            DB::table('auth_permission')->updateOrInsert(
                ['codename' => $perm['codename']],
                ['name' => $perm['name'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('auth_permission')->whereIn('codename', [
            'sp_pelanggaran',
            'sp_pelanggaran_admin',
            'sp_pelanggaran_dh',
            'sp_pelanggaran_ir_staff',
            'sp_pelanggaran_ir_head',
        ])->delete();
    }
}
