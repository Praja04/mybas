<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrWorkingtimeandovertimeStagingTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hr_workingtimeandovertime_staging')) {
            return;
        }

        Schema::create('hr_workingtimeandovertime_staging', function (Blueprint $table) {
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
            $table->string('batch_id', 50)->nullable()->index();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_workingtimeandovertime_staging');
    }
}
