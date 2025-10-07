<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKedatanganBerasStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kedatangan_beras_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('id_stock')->nullable();
            $table->date('tanggal')->nullable();
            $table->integer('jumlah_stock')->nullable();
            $table->enum('status', ['in', 'out', 'pending'])->nullable();
            $table->enum('status_approval', ['y', 'n'])->nullable();
            $table->date('approved_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamps();
        });

        // satuan nya apakah perlu ditambahkan
        // keterangan apakah perlu ditambahkab
        // satuan nya apakah perlu ditambahkan
        // belum di migrate
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kedatangan_beras_stocks');
    }
}
