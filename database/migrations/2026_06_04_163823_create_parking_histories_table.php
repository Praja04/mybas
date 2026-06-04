<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParkingHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parking_histories', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 15)->index();
            $table->string('sn_card')->index();
            $table->string('nama');
            $table->timestamp('tapped_at');
            $table->enum('status', ['IN', 'OUT']);
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
        Schema::dropIfExists('parking_histories');
    }
}
