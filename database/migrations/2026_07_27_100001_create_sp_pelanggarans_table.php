<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpPelanggaransTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('sp_pelanggarans')) {
            Schema::create('sp_pelanggarans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id')->index();
                $table->string('no_sp')->nullable();
                $table->date('tanggal_pelanggaran');
                $table->string('jenis_pelanggaran'); // Teguran Lisan, Teguran Tertulis, SP 1, SP 2, SP 3
                $table->string('status')->default('DRAFT'); // DRAFT, SELESAI
                $table->text('alasan')->nullable();
                $table->string('lampiran')->nullable(); // File path
                $table->boolean('sesuai_ketentuan')->default(true);
                $table->boolean('reported_to_admin')->default(false);
                $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
                $table->string('email_dept_head')->nullable();
                $table->string('email_dept_hr')->nullable();
                $table->string('email_dept_user')->nullable();
                
                // Approval Workflow Columns
                $table->string('sumber_data')->nullable();
                $table->text('pasal_dilanggar')->nullable();
                $table->text('uraian_pelanggaran')->nullable();
                $table->string('current_status')->default('DRAFT'); // DRAFT, PENDING_DH, PENDING_IR, PENDING_IR_HEAD, APPROVED, REJECTED
                
                $table->unsignedBigInteger('assigned_dept_head_id')->nullable()->index();
                $table->timestamp('dept_head_approved_at')->nullable();
                $table->text('dept_head_notes')->nullable();
                
                $table->unsignedBigInteger('ir_staff_id')->nullable()->index();
                $table->text('ir_staff_notes')->nullable();
                
                $table->timestamp('ir_head_approved_at')->nullable();
                $table->text('ir_head_notes')->nullable();
                
                $table->string('nomor_sp_generated')->nullable();
                $table->char('email_sent', 1)->default('N');
                $table->timestamp('email_sent_at')->nullable();
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sp_pelanggarans');
    }
};
