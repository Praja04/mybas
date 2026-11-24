<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrPmsEmployeeSnapshotTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_pms_employee_snapshot')) {
            Schema::create('hr_pms_employee_snapshot', function (Blueprint $table) {
                $table->id();
                $table->string('periode', 10);
                $table->string('nik', 50);

                $table->string('nama', 150)->nullable();
                $table->string('jabatan', 200)->nullable();
                $table->string('departemen', 150)->nullable();
                $table->string('sub_departemen', 150)->nullable();
                $table->string('section', 150)->nullable();
                $table->string('level', 10)->nullable();
                $table->string('level_group', 10)->nullable();

                $table->string('manager_nik', 50)->nullable();
                $table->string('manager_nama', 150)->nullable();
                $table->string('associate_manager_nik', 50)->nullable();
                $table->string('associate_manager_nama', 150)->nullable();
                $table->integer('jumlah_bawahan')->nullable();

                $table->string('pendidikan', 150)->nullable();
                $table->date('tanggal_join')->nullable();
                $table->date('tanggal_lahir')->nullable();
                $table->unsignedTinyInteger('usia')->nullable();

                $table->string('source', 20)->nullable();
                $table->timestamp('captured_at')->nullable();
                $table->timestamps();

                $table->unique(['periode', 'nik'], 'hr_pms_employee_snapshot_periode_nik_uq');
                $table->index('nik', 'hr_pms_employee_snapshot_nik_idx');
                $table->index(['periode', 'departemen'], 'hr_pms_employee_snapshot_periode_dept_idx');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('hr_pms_employee_snapshot');
    }
}
