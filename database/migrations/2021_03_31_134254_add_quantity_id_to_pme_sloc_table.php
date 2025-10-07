<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityIdToPmeSlocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pme')->table('pme_sloc', function (Blueprint $table) {
            $table->string('quantity_id', 150)->after('sloc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pme')->table('pme_sloc', function (Blueprint $table) {
            $table->removeColumn('quantity_id');
        });
    }
}
