<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePmeSlocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pme')->create('pme_sloc', function (Blueprint $table) {
            $table->id();
            $table->string('plant', 10);
            $table->string('sloc', 10);
            $table->string('power_meter', 250);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pme')->dropIfExists('pme_sloc');
    }
}
