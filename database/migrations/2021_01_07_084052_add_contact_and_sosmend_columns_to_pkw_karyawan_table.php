<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactAndSosmendColumnsToPkwKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pkw_karyawan', function (Blueprint $table) {
            $table->string('nomor_hp', 15)->after('tanggal_lahir')->nullable();
            $table->string('email', 255)->after('nomor_hp')->nullable();
            $table->string('sosmed_facebook', 255)->after('email')->nullable();
            $table->string('sosmed_twitter', 255)->after('email')->nullable();
            $table->string('sosmed_linkedin', 255)->after('email')->nullable();
            $table->string('sosmed_instagram', 255)->after('email')->nullable();
            // $table->string('pendidikan', 255)->after('sosmed_instagram')->nullable();
            $table->string('nama_sekolah', 255)->after('pendidikan')->nullable();
            $table->string('jurusan', 255)->after('nama_sekolah')->nullable();
            $table->string('kursus', 255)->after('jurusan')->nullable();
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
