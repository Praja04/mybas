<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLunchBreakHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lunch_break_histories', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 15)->index();
            $table->string('nama', 100);
            $table->string('divisi', 100)->nullable();
            $table->timestamp('jam_keluar')->nullable();
            $table->timestamp('jam_masuk')->nullable();
            $table->integer('menit_terlambat')->default(0);
            $table->enum('status', ['Belum Kembali', 'Tepat Waktu', 'Terlambat'])->default('Belum Kembali');
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
        Schema::dropIfExists('lunch_break_histories');
    }
}
