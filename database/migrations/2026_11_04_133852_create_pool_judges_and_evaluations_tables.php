<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoolJudgesAndEvaluationsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Master Dewan Juri
        if (!Schema::hasTable('pool_judges')) {
            Schema::create('pool_judges', function (Blueprint $table) {
                $table->id();
                $table->string('rfid')->nullable()->index();
                $table->string('nik')->unique();
                $table->string('name');
                $table->string('department')->nullable();
                $table->string('title')->nullable(); // Jabatan / Peran Juri
                $table->integer('vote_weight')->default(10);
                $table->longText('photo')->nullable(); // Foto blob / base64
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Penilaian Booth Pemenang oleh Dewan Juri (Skor 0 - 100 per Booth)
        if (!Schema::hasTable('pool_judge_evaluations')) {
            Schema::create('pool_judge_evaluations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('judge_id');
                $table->unsignedBigInteger('booth_id');
                $table->decimal('score', 5, 2)->default(0); // Nilai 0.00 - 100.00
                $table->text('notes')->nullable(); // Catatan masukan dari juri
                $table->timestamp('evaluated_at')->nullable();
                $table->timestamps();

                $table->foreign('judge_id')->references('id')->on('pool_judges')->onDelete('cascade');
                $table->foreign('booth_id')->references('id')->on('pool_booths')->onDelete('cascade');

                // 1 juri hanya memiliki 1 evaluasi per booth (bisa diupdate)
                $table->unique(['judge_id', 'booth_id']);
            });
        }

        // 3. Tambahan kolom is_judge_vote di pool_votes jika belum ada
        if (Schema::hasTable('pool_votes') && !Schema::hasColumn('pool_votes', 'is_judge_vote')) {
            Schema::table('pool_votes', function (Blueprint $table) {
                $table->boolean('is_judge_vote')->default(false)->after('points_awarded');
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
        if (Schema::hasTable('pool_votes') && Schema::hasColumn('pool_votes', 'is_judge_vote')) {
            Schema::table('pool_votes', function (Blueprint $table) {
                $table->dropColumn('is_judge_vote');
            });
        }
        Schema::dropIfExists('pool_judge_evaluations');
        Schema::dropIfExists('pool_judges');
    }
}
