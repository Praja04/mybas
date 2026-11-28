<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpApprovalLogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('sp_approval_logs')) {
            Schema::create('sp_approval_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sp_pelanggaran_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('action'); // SUBMIT, DEPT_HEAD_APPROVE, DEPT_HEAD_REJECT, IR_STAFF_SUBMIT, IR_HEAD_APPROVE, IR_HEAD_REJECT, EMAIL_SENT
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sp_approval_logs');
    }
};
