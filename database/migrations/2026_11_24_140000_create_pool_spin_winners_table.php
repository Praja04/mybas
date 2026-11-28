<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoolSpinWinnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pool_spin_winners')) {
            Schema::create('pool_spin_winners', function (Blueprint $table) {
                $table->id();
                $table->string('tier_key')->index(); // 'reward_1', 'reward_2', etc.
                $table->unsignedBigInteger('participant_id')->nullable()->index();
                $table->string('name');
                $table->string('nik')->nullable()->index();
                $table->string('department')->nullable();
                $table->string('employee_type')->nullable();
                $table->boolean('is_non_staff')->default(false);
                $table->unsignedInteger('weight')->default(1);
                $table->unsignedInteger('total_score')->default(0);
                $table->unsignedTinyInteger('booths_voted_count')->default(0);
                $table->unsignedSmallInteger('order')->default(1);
                $table->string('won_at')->nullable();
                $table->timestamps();

                $table->foreign('participant_id')->references('id')->on('pool_participants')->onDelete('set null');
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
        Schema::dropIfExists('pool_spin_winners');
    }
}
