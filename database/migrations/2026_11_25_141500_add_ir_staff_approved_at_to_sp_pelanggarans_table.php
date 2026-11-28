<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIrStaffApprovedAtToSpPelanggaransTable extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sp_pelanggarans')) {
            Schema::table('sp_pelanggarans', function (Blueprint $table) {
                if (!Schema::hasColumn('sp_pelanggarans', 'ir_staff_approved_at')) {
                    $table->timestamp('ir_staff_approved_at')->nullable()->after('ir_staff_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sp_pelanggarans')) {
            Schema::table('sp_pelanggarans', function (Blueprint $table) {
                if (Schema::hasColumn('sp_pelanggarans', 'ir_staff_approved_at')) {
                    $table->dropColumn('ir_staff_approved_at');
                }
            });
        }
    }
}

