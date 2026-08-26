<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropNoSpFromSpPelanggaransTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('sp_pelanggarans', 'no_sp')) {
            Schema::table('sp_pelanggarans', function (Blueprint $table) {
                $table->dropColumn('no_sp');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('sp_pelanggarans', 'no_sp')) {
            Schema::table('sp_pelanggarans', function (Blueprint $table) {
                $table->string('no_sp')->nullable()->after('employee_id');
            });
        }
    }
}
