<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpAuditMangkirsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sp_audit_mangkirs', function (Blueprint $table) {
            $table->id();
            $table->string('periode', 20)->index(); // e.g. 2026-06
            $table->string('periode_label', 100)->nullable(); // e.g. June 2026
            $table->unsignedBigInteger('employee_id')->nullable()->index();
            $table->string('nik', 50)->index();
            $table->string('nama', 150);
            $table->string('department', 100)->nullable();
            $table->string('section', 100)->nullable();
            $table->date('tanggal')->index();
            $table->integer('mangkir_ke')->default(1);
            $table->string('kode_absensi', 20)->default('A');
            $table->string('filename', 255)->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            // Composite index for fast duplicate checks
            $table->index(['periode', 'nik', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sp_audit_mangkirs');
    }
}
