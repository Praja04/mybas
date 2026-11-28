<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFotoOutAndKondisiKacamataOutToGaVisitorVendorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ga_visitor_vendor', function (Blueprint $table) {
            $table->text('foto_out')->nullable()->after('foto');
            $table->string('kondisi_kacamata_out')->nullable()->after('kondisi_kacamata');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ga_visitor_vendor', function (Blueprint $table) {
            $table->dropColumn(['foto_out', 'kondisi_kacamata_out']);
        });
    }
}
