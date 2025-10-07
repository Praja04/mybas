<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionIdColumnToSigraSertifikasiLegalitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sigra_sertifikasi_legalitas', function (Blueprint $table) {
            $table->string('transaction_id', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sigra_sertifikasi_legalitas', function (Blueprint $table) {
            //
        });
    }
}
