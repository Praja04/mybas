<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrWorkingtimeandovertimeTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hr_workingtimeandovertime')) {
            return;
        }

        Schema::create('hr_workingtimeandovertime', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 50)->index();
            $table->string('nama', 150);
            $table->string('dept', 100)->nullable();
            $table->string('section', 100)->nullable();
            $table->date('tgl_in');
            $table->decimal('jam_spkl', 5, 2)->nullable();
            $table->decimal('jam_hovt', 5, 2)->nullable();
            $table->string('no_spkl', 100)->index();
            $table->string('send_by_username', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_workingtimeandovertime');
    }
}
