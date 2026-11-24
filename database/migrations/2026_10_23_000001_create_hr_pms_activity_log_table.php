<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrPmsActivityLogTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_pms_activity_log')) {
            Schema::create('hr_pms_activity_log', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_nik', 50)->nullable();
                $table->string('user_username', 100)->nullable();
                $table->string('user_name', 150)->nullable();
                $table->string('module', 60)->nullable();
                $table->string('action', 30)->nullable();
                $table->string('action_label', 150)->nullable();
                $table->string('route_action', 150)->nullable();
                $table->string('http_method', 10)->nullable();
                $table->string('uri', 500)->nullable();
                $table->string('target_type', 30)->nullable();
                $table->unsignedBigInteger('target_id')->nullable();
                $table->string('target_nik', 50)->nullable();
                $table->text('meta')->nullable();
                $table->unsignedSmallInteger('status_code')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 255)->nullable();
                $table->timestamp('created_at')->nullable();

                $table->index(['user_nik', 'created_at'], 'hr_pms_activity_log_user_nik_created_idx');
                $table->index(['module', 'action', 'created_at'], 'hr_pms_activity_log_module_action_idx');
                $table->index(['target_nik', 'created_at'], 'hr_pms_activity_log_target_nik_idx');
                $table->index('created_at', 'hr_pms_activity_log_created_idx');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('hr_pms_activity_log')) {
            Schema::dropIfExists('hr_pms_activity_log');
        }
    }
}
