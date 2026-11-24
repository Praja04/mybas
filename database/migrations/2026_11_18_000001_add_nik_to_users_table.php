<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNikToUsersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'nik')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nik', 100)->nullable()->after('id');
                $table->index('nik', 'users_nik_index');
            });
        }

        // Backfill hanya untuk username yang persis sama dengan NIK master.
        // Akun lain dirapikan manual lewat master user.
        if (Schema::hasTable('hr_master_employee')) {
            DB::statement("
                UPDATE users u
                INNER JOIN hr_master_employee e ON e.NIK = u.username
                SET u.nik = e.NIK
                WHERE u.nik IS NULL OR u.nik = ''
            ");
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'nik')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_nik_index');
                $table->dropColumn('nik');
            });
        }
    }
}
