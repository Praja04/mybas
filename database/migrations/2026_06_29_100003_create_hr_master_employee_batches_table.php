<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrMasterEmployeeBatchesTable extends Migration
{
    public function up()
    {
        Schema::create('hr_master_employee_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id', 50)->unique();
            $table->string('filename');
            $table->string('send_by_username');
            $table->integer('total_data')->default(0);
            $table->integer('created_count')->default(0);
            $table->integer('updated_count')->default(0);
            $table->integer('confirmed_count')->default(0);
            $table->string('status', 20)->default('pending');
            $table->string('file_path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_master_employee_batches');
    }
}
