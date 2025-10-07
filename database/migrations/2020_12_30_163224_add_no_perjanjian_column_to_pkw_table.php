<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoPerjanjianColumnToPkwTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pkw', function (Blueprint $table) {
            $table->string('no_perjanjian', 50)->default(0)->after('tanggal_phk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pkw', function (Blueprint $table) {
            //
        });
    }
}
