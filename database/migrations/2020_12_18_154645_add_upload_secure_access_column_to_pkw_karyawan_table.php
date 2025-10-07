<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUploadSecureAccessColumnToPkwKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pkw_karyawan', function (Blueprint $table) {
            $table->string('upload_secure_access', 20)->default('belum');
            $table->datetime('upload_secure_access_time')->nullable();
            $table->string('upload_secure_access_by', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pkw_karyawan', function (Blueprint $table) {
            //
        });
    }
}
