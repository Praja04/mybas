<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternalMemoApproverUtamaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internal_memo_approver_utama', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pos_id', 50);
            $table->string('divisi', 100);
            $table->string('bagian', 100);
            $table->string('nik', 100);
            $table->integer('level');
            $table->string('report_to', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('internal_memo_approver_utama');
    }
}
