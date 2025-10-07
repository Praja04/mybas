<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePmeDatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pme')->create('pme_datalog', function (Blueprint $table) {
            $table->id();
            $table->string('name', 250);
            $table->string('source_id', 10);
            $table->string('quantity_id', 10);
            $table->string('dept', 15);
            $table->date('date');
            $table->enum('shift', ['1','2','3']);
            $table->integer('value');
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
        Schema::connection('pme')->dropIfExists('pme_datalog');
    }
}
