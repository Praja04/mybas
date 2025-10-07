<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToPmeSlocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pme')->table('pme_sloc', function (Blueprint $table) {
            $table->enum('status', ['1','0'])->after('department_name')->default('1');
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
            $table->removeColumn('status');
        });
    }
}
