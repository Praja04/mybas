<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrPmsAccessOtpLocksTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hr_pms_access_otp_locks')) {
            return;
        }

        Schema::create('hr_pms_access_otp_locks', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_pms_access_otp_locks');
    }
}
