<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToPoolJudgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('pool_judges') && !Schema::hasColumn('pool_judges', 'user_id')) {
            Schema::table('pool_judges', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id')->index();
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
        if (Schema::hasTable('pool_judges') && Schema::hasColumn('pool_judges', 'user_id')) {
            Schema::table('pool_judges', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
}
