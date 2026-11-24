<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrPmsAccessOtpTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hr_pms_access_otp')) {
            return;
        }

        Schema::create('hr_pms_access_otp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('request_id')->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('nik', 50)->nullable();
            $table->string('intended_uri', 1000)->nullable();
            $table->string('purpose', 30)->default('pms_access');
            $table->string('code_hash', 255)->nullable();
            $table->dateTime('request_expires_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedSmallInteger('send_count')->default(0);
            $table->dateTime('last_sent_at')->nullable();
            $table->dateTime('consumed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'purpose', 'consumed_at'], 'hr_pms_access_otp_user_purpose_idx');
            $table->index('expires_at', 'hr_pms_access_otp_expires_idx');
            $table->index('request_expires_at', 'hr_pms_access_otp_request_expires_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_pms_access_otp');
    }
}
