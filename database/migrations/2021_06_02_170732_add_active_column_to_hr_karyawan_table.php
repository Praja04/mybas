<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActiveColumnToHrKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hr_karyawan', function (Blueprint $table) {
            $table->enum('active', ['Y', 'N'])->after('journal_group')->default('Y');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_karyawan', function (Blueprint $table) {
            $table->removeColumn('active');
        });
    }
}
