<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdWorkspaceTo5rMasterDepartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('5r_master_department', function (Blueprint $table) {
            $table->string('id_workspace', 3)->after('id_department')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('5r_master_department', function (Blueprint $table) {
            $table->dropColumn('id_workspace');
        });
    }
}
