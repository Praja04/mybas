<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternalMemoTtdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internal_memo_ttd', function (Blueprint $table) {
           $table->increments('id');
            $table->integer('id_im');
            $table->string('no_dokumen', 100);
            $table->string('nik_tujuan', 100);
            $table->string('dept_tujuan', 100);
            $table->string('kategori', 100);
            $table->string('sub_kategori', 100);
            $table->date('tgl_ttd');
            $table->time('jam_ttd');
            $table->string('alasan_tolak', 255);
            $table->integer('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('internal_memo_ttd');
    }
}
