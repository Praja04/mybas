<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpKodePelanggaransTable extends Migration
{
    public function up()
    {
        Schema::create('sp_kode_pelanggarans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode', 50)->unique();
            $table->string('nama_pelanggaran');
            $table->string('jenis_sp'); // Teguran Lisan, SP 1, SP 2, SP 3
            $table->text('pasal_dilanggar')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sp_kode_pelanggarans');
    }
}
