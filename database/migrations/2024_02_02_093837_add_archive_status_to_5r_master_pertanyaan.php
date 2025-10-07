<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchiveStatusTo5rMasterPertanyaan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('5r_master_pertanyaan', function (Blueprint $table) {
            $table->enum('archive_status', ['Y', 'N'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('5r_master_pertanyaan', function (Blueprint $table) {
            $table->dropColumn('archive_status');
        });
    }
}
