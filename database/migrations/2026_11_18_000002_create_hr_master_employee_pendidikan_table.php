<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrMasterEmployeePendidikanTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hr_master_employee_pendidikan')) {
            return;
        }

        Schema::create('hr_master_employee_pendidikan', function (Blueprint $table) {
            $table->string('nik')->primary();
            $table->string('nama')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('nama_sekolah')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('ipk', 20)->nullable();
            $table->string('send_by_username')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_master_employee_pendidikan');
    }
}
