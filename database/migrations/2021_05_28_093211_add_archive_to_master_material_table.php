<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchiveToMasterMaterialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('men_master_material', function (Blueprint $table) {
            $table->enum('archive', ['Y', 'N'])->default('N');
            $table->foreignId('upload_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('men_master_material', function (Blueprint $table) {
            $table->removeColumn('archive');
            $table->removeColumn('upload_id');
        });
    }
}
