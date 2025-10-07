<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternalMemoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internal_memo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_dokumen', 150);
            $table->date('tgl_pengisian');
            $table->string('nik_pembuat', 20);
            $table->string('dept_pembuat', 20);
            $table->string('rev', 20)->default('00');
            $table->string('perihal', 150);
            $table->text('konten');
            $table->time('jam_pengisian');
            $table->date('tgl_edit');
            $table->time('jam_edit');
            $table->string('nama_pengisi', 50);
            $table->string('nik_pengisi', 20);
            $table->integer('status')->default(1);
            $table->string('progress', 150);
            $table->string('alasan_tolak', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('internal_memo');
    }
}
