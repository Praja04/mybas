<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrIppManagerMappingTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_ipp_manager_mapping')) {
            Schema::create('hr_ipp_manager_mapping', function (Blueprint $table) {
                $table->id();
                $table->string('sub_departmen', 150)->unique();
                $table->string('nik_manager', 50)->nullable();
                $table->string('nama_manager', 150)->nullable();
                $table->string('created_by', 100)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('hr_ipp_manager_mapping');
    }
}