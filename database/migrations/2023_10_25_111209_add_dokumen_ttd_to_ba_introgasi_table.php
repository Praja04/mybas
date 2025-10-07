<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDokumenTtdToBaIntrogasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ba_introgasi', function (Blueprint $table) {
            $table->string('dokumen_ttd')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ba_introgasi', function (Blueprint $table) {
            $table->dropColumn('dokumen_ttd');
        });
    }
}
