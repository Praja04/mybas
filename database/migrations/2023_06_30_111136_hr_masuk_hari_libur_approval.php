<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HrMasukHariLiburApproval extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_masuk_hari_libur_approval', function (Blueprint $table) {
            $table->id();
            $table->string('dept');
            $table->string('nik_admin');
            $table->string('nama_admin');
            $table->string('nik_approval');
            $table->string('nama_approval');
            $table->enum('status', ['aktif', 'tidak aktif'])->default('aktif');
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
        Schema::dropIfExists('hr_masuk_hari_libur_approval');
    }
}
