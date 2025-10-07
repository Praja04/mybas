<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBerasStocksTable extends Migration
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
            $table->date('tanggal');
            $table->integer('kedatangan_stock');
            $table->enum('status_approval', ['y', 'n'])->default('n');
            $table->enum('approval_cek_stock', ['belum', 'menunggu_approval', 'sudah']);
            $table->date('approval_cek_stock_at')->nullable();
            $table->string('approval_cek_stock_by')->nullable();
            $table->string('approval_pemakaian_beras')->nullable();
            $table->date('approval_pemakaian_beras_at')->nullable();
            $table->string('approval_pemakaian_beras_by')->nullable();
            $table->string('approval_pengambilan_beras')->nullable();
            $table->date('approval_pengambilan_beras_at')->nullable();
            $table->string('approval_pengambilan_beras_by')->nullable();
            $table->timestamps();
        });

        // satuan nya apakah perlu ditambahkan
        // keterangan apakah perlu ditambahkab
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beras_stocks');
    }
}
