<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePoolEvaluationCriteriaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Tabel Master Kriteria Penilaian Booth
        if (!Schema::hasTable('pool_evaluation_criteria')) {
            Schema::create('pool_evaluation_criteria', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('weight', 5, 2)->default(25.00); // Bobot dalam % (misal 25.00)
                $table->decimal('max_score', 5, 2)->default(100.00); // Skor maksimal
                $table->integer('order')->default(0); // Urutan tampil
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Tambah kolom snapshot json criteria_scores di pool_judge_evaluations jika belum ada
        if (Schema::hasTable('pool_judge_evaluations') && !Schema::hasColumn('pool_judge_evaluations', 'criteria_scores')) {
            Schema::table('pool_judge_evaluations', function (Blueprint $table) {
                $table->longText('criteria_scores')->nullable()->after('score');
            });
        }

        // 3. Detail penilaian per kriteria
        if (!Schema::hasTable('pool_judge_evaluation_details')) {
            Schema::create('pool_judge_evaluation_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('evaluation_id');
                $table->unsignedBigInteger('criterion_id');
                $table->decimal('score', 5, 2)->default(0);
                $table->decimal('weight', 5, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('evaluation_id')->references('id')->on('pool_judge_evaluations')->onDelete('cascade');
                $table->foreign('criterion_id')->references('id')->on('pool_evaluation_criteria')->onDelete('cascade');

                $table->unique(['evaluation_id', 'criterion_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pool_judge_evaluation_details');
        if (Schema::hasTable('pool_judge_evaluations') && Schema::hasColumn('pool_judge_evaluations', 'criteria_scores')) {
            Schema::table('pool_judge_evaluations', function (Blueprint $table) {
                $table->dropColumn('criteria_scores');
            });
        }
        Schema::dropIfExists('pool_evaluation_criteria');
    }
}
