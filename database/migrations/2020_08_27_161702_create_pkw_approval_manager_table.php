<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePkwApprovalManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkw_approval_manager', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bagian');
            $table->foreignId('approval1')->nullable();
            $table->foreignId('approval2')->nullable();
            $table->foreignId('approval3')->nullable();
            $table->timestamps();
            $table->foreign('id_bagian')->references('id')->on('pkw_bagian');
            $table->foreign('approval1')->references('id')->on('users');
            $table->foreign('approval2')->references('id')->on('users');
            $table->foreign('approval3')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pkw_approval_manager');
    }
}
