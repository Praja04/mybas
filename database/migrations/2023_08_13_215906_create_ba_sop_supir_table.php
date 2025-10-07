<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaSopSupirTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ba_sop_supir', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 70);
            $table->string('ekspedisi', 50);
            $table->string('no_ktp', 15);
            $table->string('no_polisi', 15);
            $table->string('no_handphone', 15);
            $table->string('no_kartu', 15);
            $table->text('alamat');
            $table->enum('shift', ['1', '2', '3']);
            $table->string('nama_pembuat', 100);
            $table->string('jabatan_pembuat', 40);
            $table->timestamps();
            $table->dateTime('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ba_sop_supir');
    }
}
