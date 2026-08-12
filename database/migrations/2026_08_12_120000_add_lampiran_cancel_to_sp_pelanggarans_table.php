<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLampiranCancelToSpPelanggaransTable extends Migration
{
    public function up()
    {
        Schema::table('sp_pelanggarans', function (Blueprint $table) {
            if (!Schema::hasColumn('sp_pelanggarans', 'lampiran_cancel')) {
                $table->string('lampiran_cancel')->nullable()->after('lampiran');
            }
        });
    }

    public function down()
    {
        Schema::table('sp_pelanggarans', function (Blueprint $table) {
            if (Schema::hasColumn('sp_pelanggarans', 'lampiran_cancel')) {
                $table->dropColumn('lampiran_cancel');
            }
        });
    }
}
