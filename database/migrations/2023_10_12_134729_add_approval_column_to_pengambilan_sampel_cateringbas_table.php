<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalColumnToPengambilanSampelCateringbasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengambilan_sampel_cateringbas', function (Blueprint $table) {
            $table->enum('status_approval', ['Y', 'N'])->default('N');
            $table->dateTime('approved_at')->nullable();
            $table->string('approved_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengambilan_sampel_cateringbas', function (Blueprint $table) {
            $table->dropColumn('status_approval');
            $table->dropColumn('approved_at');
            $table->dropColumn('approved_by');
        });
    }
}
