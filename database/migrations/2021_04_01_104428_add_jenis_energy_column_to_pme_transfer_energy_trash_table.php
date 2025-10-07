<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenisEnergyColumnToPmeTransferEnergyTrashTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pme')->table('pme_transfer_energy_trash', function (Blueprint $table) {
            $table->enum('jenis_energy', ['listrik', 'steam', 'batubara'])->after('destination_sloc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pme')->table('pme_transfer_energy_trash', function (Blueprint $table) {
            $table->removeColumn('jenis_energy');
        });
    }
}
