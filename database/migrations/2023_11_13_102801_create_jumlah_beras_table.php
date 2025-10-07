<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJumlahBerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jumlah_beras', function (Blueprint $table) {
            $table->id();
            $table->string('id_stock')->nullable();
            $table->date('tanggal')->nullable();
            $table->float('jumlah_stock')->nullable();
            $table->string('satuan_berat', 250)->nullable();
            $table->enum('status', ['in', 'out', 'pending'])->nullable();
            $table->enum('active', ['Y', 'N'])->nullable();
            $table->enum('status_approval', ['y', 'n'])->nullable();
            $table->date('approved_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks_beras');
    }
}
