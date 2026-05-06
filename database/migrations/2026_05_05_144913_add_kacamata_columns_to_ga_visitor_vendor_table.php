<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKacamataColumnsToGaVisitorVendorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ga_visitor_vendor', function (Blueprint $table) {
            $table->boolean('is_kacamata')->default(0)->after('qr_code_saat_ini');
            $table->string('kondisi_kacamata')->nullable()->after('is_kacamata');
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
            $table->dropColumn(['is_kacamata', 'kondisi_kacamata']);
        });
    }
}
