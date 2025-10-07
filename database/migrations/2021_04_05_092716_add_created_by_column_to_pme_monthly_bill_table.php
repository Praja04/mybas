<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByColumnToPmeMonthlyBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pme')->table('pme_monthly_bill', function (Blueprint $table) {
            $table->string('created_by', 15)->after('kwh');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pme')->table('pme_monthly_bill', function (Blueprint $table) {
            $table->removeColumn('created_by', 15);
        });
    }
}
