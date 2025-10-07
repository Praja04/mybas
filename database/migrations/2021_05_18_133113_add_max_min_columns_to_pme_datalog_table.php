<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxMinColumnsToPmeDatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pme')->table('pme_datalog', function (Blueprint $table) {
            $table->double('min_value')->after('value')->nullable();
            $table->double('max_value')->after('value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pme')->table('pme_datalog', function (Blueprint $table) {
            $table->removeColumn('min_value');
            $table->removeColumn('max_value');
        });
    }
}
