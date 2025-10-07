<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusTanggalColumnToBerasPemakaiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beras_pemakaians', function (Blueprint $table) {
            $table->enum('status', ['in', 'out', 'pending'])->default('pending')->nullable();
            $table->dateTime('tanggal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beras_pemakaians', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('tanggal');
        });
    }
}
