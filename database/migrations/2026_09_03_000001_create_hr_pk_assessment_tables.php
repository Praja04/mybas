<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateHrPkAssessmentTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_pk_nilai')) {
            Schema::create('hr_pk_nilai', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hr_pk_id');
                $table->unsignedBigInteger('hr_pk_detail_id');
                $table->string('nik', 50);
                $table->string('nama', 150)->nullable();
                $table->unsignedTinyInteger('nilai_self')->nullable();
                $table->string('self_by', 100)->nullable();
                $table->timestamp('self_at')->nullable();
                $table->unsignedTinyInteger('nilai_leader')->nullable();
                $table->string('leader_by', 100)->nullable();
                $table->timestamp('leader_at')->nullable();
                $table->boolean('review')->default(false);
                $table->string('reviewed_by', 100)->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->string('am_approved_by', 50)->nullable();
                $table->timestamp('am_approved_at')->nullable();
                $table->string('mgr_approved_by', 50)->nullable();
                $table->timestamp('mgr_approved_at')->nullable();
                $table->timestamps();

                $table->foreign('hr_pk_id')->references('id')->on('hr_pk')->onDelete('cascade');
                $table->foreign('hr_pk_detail_id')->references('id')->on('hr_pk_detail')->onDelete('cascade');
                $table->unique(['hr_pk_id', 'hr_pk_detail_id', 'nik'], 'hr_pk_nilai_pk_detail_nik_uq');
                $table->index('nik', 'hr_pk_nilai_nik_idx');
            });
        }

        if (!Schema::hasTable('hr_pk_diskusi')) {
            Schema::create('hr_pk_diskusi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hr_pk_id');
                $table->string('nik_yang_dibahas', 50);
                $table->unsignedBigInteger('hr_pk_detail_id')->nullable();
                $table->string('nik_pengaju', 50);
                $table->string('nama_pengaju', 150);
                $table->text('pesan');
                $table->text('jawaban')->nullable();
                $table->string('nik_penjawab', 50)->nullable();
                $table->string('nama_penjawab', 150)->nullable();
                $table->string('status', 20)->default('open');
                $table->timestamp('answered_at')->nullable();
                $table->timestamps();

                $table->foreign('hr_pk_id')->references('id')->on('hr_pk')->onDelete('cascade');
                $table->foreign('hr_pk_detail_id')->references('id')->on('hr_pk_detail')->onDelete('cascade');
                $table->index(['hr_pk_id', 'nik_yang_dibahas'], 'hr_pk_diskusi_pk_ybs_idx');
            });
        }

        if (!Schema::hasTable('hr_pk_associate_manager_mapping')) {
            Schema::create('hr_pk_associate_manager_mapping', function (Blueprint $table) {
                $table->id();
                $table->string('nik', 50)->unique();
                $table->string('nama', 150)->nullable();
                $table->string('nik_associate_manager', 50);
                $table->string('nama_associate_manager', 150)->nullable();
                $table->string('created_by', 100)->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('hr_pk_nilai')) {
            $approvalColumns = [
                'am_approved_by' => function (Blueprint $t) {
                    $t->string('am_approved_by', 50)->nullable();
                },
                'am_approved_at' => function (Blueprint $t) {
                    $t->timestamp('am_approved_at')->nullable();
                },
                'mgr_approved_by' => function (Blueprint $t) {
                    $t->string('mgr_approved_by', 50)->nullable();
                },
                'mgr_approved_at' => function (Blueprint $t) {
                    $t->timestamp('mgr_approved_at')->nullable();
                },
            ];
            foreach ($approvalColumns as $col => $def) {
                if (!Schema::hasColumn('hr_pk_nilai', $col)) {
                    Schema::table('hr_pk_nilai', $def);
                }
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('hr_pk_associate_manager_mapping') && !DB::table('hr_pk_associate_manager_mapping')->exists()) {
            Schema::dropIfExists('hr_pk_associate_manager_mapping');
        }
        if (Schema::hasTable('hr_pk_diskusi') && !DB::table('hr_pk_diskusi')->exists()) {
            Schema::dropIfExists('hr_pk_diskusi');
        }
        if (Schema::hasTable('hr_pk_nilai') && !DB::table('hr_pk_nilai')->exists()) {
            Schema::dropIfExists('hr_pk_nilai');
        }
    }
}
