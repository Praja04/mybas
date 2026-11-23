<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSpinExcludedToPoolParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('pool_participants') && !Schema::hasColumn('pool_participants', 'is_spin_excluded')) {
            Schema::table('pool_participants', function (Blueprint $table) {
                $table->boolean('is_spin_excluded')->default(false)->after('booths_voted_count')->index();
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
        if (Schema::hasTable('pool_participants') && Schema::hasColumn('pool_participants', 'is_spin_excluded')) {
            Schema::table('pool_participants', function (Blueprint $table) {
                $table->dropColumn('is_spin_excluded');
            });
        }
    }
}
