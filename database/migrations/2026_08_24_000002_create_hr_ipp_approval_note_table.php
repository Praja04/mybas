<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrIppApprovalNoteTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_ipp_approval_note')) {
            Schema::create('hr_ipp_approval_note', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ipp_otorisasi_id');
                $table->string('tahapan', 20);
                $table->string('role', 20);
                $table->text('note')->nullable();
                $table->timestamps();

                $table->foreign('ipp_otorisasi_id')->references('id')->on('hr_ipp_otorisasi')->onDelete('cascade');
                $table->unique(['ipp_otorisasi_id', 'tahapan', 'role'], 'hr_ipp_approval_note_oto_tahapan_role_unique');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('hr_ipp_approval_note');
    }
}
