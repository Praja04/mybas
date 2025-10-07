<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityIdToPmeTransferEnergyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pme')->table('pme_transfer_energy', function (Blueprint $table) {
            $table->string('source_quantity_id', 100)->after('source_sloc');
            $table->string('destination_quantity_id', 100)->after('source_sloc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pme')->table('pme_transfer_energy', function (Blueprint $table) {
            $table->removeColumn('source_quantity_id');
            $table->removeColumn('destination_quantity_id');
        });
    }
}
