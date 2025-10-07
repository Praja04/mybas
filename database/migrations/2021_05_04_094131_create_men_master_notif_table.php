<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenMasterNotifTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('men_master_notif', function (Blueprint $table) {
            $table->id();
            $table->string('category', 10);
            $table->integer('shelf_life_min');
            $table->integer('shelf_life_max');
            $table->integer('days_min');
            $table->integer('days_max');
            $table->enum('status', ['expired', 'warning1', 'warning2', 'standard']);
            $table->string('decription', 150)->nullable();
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
        Schema::dropIfExists('men_master_notif');
    }
}
