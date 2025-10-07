<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePmeTransferEnergyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pme')->create('pme_transfer_energy', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('action', 250);
            $table->string('source_sloc', 10);
            $table->string('destination_sloc', 10);
            $table->bigInteger('kwh');
            $table->text('description');
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
        Schema::connection('pme')->dropIfExists('pme_transfer_energy');
    }
}
