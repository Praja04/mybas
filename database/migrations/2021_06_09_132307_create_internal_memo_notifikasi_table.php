<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternalMemoNotifikasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internal_memo_notifikasi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_notif', 100);
            $table->string('no_dokumen', 100);
            $table->integer('id_im');
            $table->string('notif_from_nik', 100);
            $table->string('notif_to_nik', 100);
            $table->string('isi', 255);
            $table->integer('status')->default(1);
            $table->integer('jenis_notif');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('internal_memo_notifikasi');
    }
}
