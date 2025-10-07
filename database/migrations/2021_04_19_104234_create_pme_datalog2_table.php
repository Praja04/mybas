<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePmeDatalog2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pme')->create('pme_datalog2', function (Blueprint $table) {
            $table->id();
            $table->float('Value');
            $table->integer('SourceID');
            $table->integer('QuantityID');
            $table->string('dept', 10);
            $table->date('date');
            $table->string('shift', 5);
            $table->datetime('TimestampUTC');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pme')->dropIfExists('pme_datalog2');
    }
}
