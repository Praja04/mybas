<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrWorkingtimeandovertimeBatchesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hr_workingtimeandovertime_batches')) {
            return;
        }

        Schema::create('hr_workingtimeandovertime_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id', 50)->unique();
            $table->string('filename');
            $table->string('send_by_username', 100);
            $table->integer('total_data')->default(0);
            $table->integer('created_count')->default(0);
            $table->integer('updated_count')->default(0);
            $table->integer('confirmed_count')->default(0);
            $table->string('confirm_status', 20)->nullable();
            $table->integer('confirm_total')->default(0);
            $table->integer('confirm_processed')->default(0);
            $table->text('confirm_error')->nullable();
            $table->string('status', 20)->default('pending');
            $table->string('file_path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_workingtimeandovertime_batches');
    }
}
