<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePmeMonthlyBillTrashTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pme')->create('pme_monthly_bill_trash', function (Blueprint $table) {
            $table->id();
            $table->string('plant', 10);
            $table->year('year');
            $table->string('month', 3);
            $table->bigInteger('kwh');
            $table->string('deleted_by', 50);
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
        Schema::connection('pme')->dropIfExists('pme_monthly_bill_trash');
    }
}
