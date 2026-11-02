<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoolQccTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Booth Master
        Schema::create('pool_booths', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->nullable(); // Departemen / Circle Kaizen
            $table->text('description')->nullable();
            $table->string('location')->nullable(); // Nomor Meja / Area Booth
            $table->string('pic_name')->nullable();
            $table->string('pic_contact')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('total_points')->default(0);
            $table->unsignedInteger('total_voters')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Pertanyaan Booth
        Schema::create('pool_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booth_id');
            $table->text('question_text');
            $table->string('image')->nullable();
            $table->text('option_a');
            $table->text('option_b');
            $table->text('option_c');
            $table->text('option_d');
            $table->text('option_e')->nullable();
            $table->string('correct_answer', 5); // A, B, C, D, E
            $table->unsignedInteger('points')->default(10);
            $table->text('explanation')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('booth_id')->references('id')->on('pool_booths')->onDelete('cascade');
        });

        // 3. Peserta Pooling
        Schema::create('pool_participants', function (Blueprint $table) {
            $table->id();
            $table->string('rfid')->index();
            $table->string('nik')->nullable()->index();
            $table->string('name');
            $table->string('department')->nullable();
            $table->longText('photo')->nullable(); // Foto / avatar
            $table->unsignedInteger('total_score')->default(0);
            $table->unsignedTinyInteger('booths_voted_count')->default(0); // Max 3
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });

        // 4. Log Vote / Pooling Sesi
        Schema::create('pool_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('participant_id');
            $table->unsignedBigInteger('booth_id');
            $table->unsignedBigInteger('question_id')->nullable();
            $table->string('participant_answer', 5)->nullable();
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('points_awarded')->default(0);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->foreign('participant_id')->references('id')->on('pool_participants')->onDelete('cascade');
            $table->foreign('booth_id')->references('id')->on('pool_booths')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('pool_questions')->onDelete('set null');

            // 1 peserta hanya boleh pool 1 booth sebanyak 1 kali
            $table->unique(['participant_id', 'booth_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pool_votes');
        Schema::dropIfExists('pool_participants');
        Schema::dropIfExists('pool_questions');
        Schema::dropIfExists('pool_booths');
    }
}
