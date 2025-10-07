<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSigraMdMiInstanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sigra_md_mi_instan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_perusahaan');
            $table->string('varian', 150);
            $table->string('no_md', 150);
            $table->string('status', 20)->default('active');
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
        Schema::dropIfExists('sigra_md_mi_instan');
    }
}
