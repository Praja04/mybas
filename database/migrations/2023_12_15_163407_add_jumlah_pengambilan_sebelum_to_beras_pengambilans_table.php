<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJumlahPengambilanSebelumToBerasPengambilansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beras_pengambilans', function (Blueprint $table) {
            $table->decimal('jumlah_pengambilan_sebelum', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beras_pengambilans', function (Blueprint $table) {
            $table->dropColumn('jumlah_pengambilan_sebelum');
        });
    }
}
