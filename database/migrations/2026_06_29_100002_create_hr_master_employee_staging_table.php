<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrMasterEmployeeStagingTable extends Migration
{
    public function up()
    {
        Schema::create('hr_master_employee_staging', function (Blueprint $table) {
            $table->string('NIK')->primary();
            $table->string('Nama');
            $table->date('Tgl Lahir');
            $table->date('Tgl Masuk');
            $table->string('Departmen');
            $table->string('Sub Departmen');
            $table->string('Section');
            $table->string('Tipe Karyawan');
            $table->string('Jabatan');
            $table->string('Jenis Kelamin');
            $table->string('Work Status');
            $table->string('Status Nikah');
            $table->string('Aktif');
            $table->date('Valid From');
            $table->string('send_by_username');
            $table->string('batch_id', 50)->nullable()->index();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_master_employee_staging');
    }
}
