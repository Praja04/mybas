<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenMaterialExcludeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('men_material_exclude', function (Blueprint $table) {
            $table->id();
            $table->string('plant', 10);
            $table->string('sloc', 10);
            $table->string('material', 10);
            $table->string('material_description', 250);
            $table->string('batch', 20);
            $table->string('uom', 10);
            $table->double('qty', 15, 2);
            $table->string('material_type');
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
        Schema::dropIfExists('men_material_exclude');
    }
}
