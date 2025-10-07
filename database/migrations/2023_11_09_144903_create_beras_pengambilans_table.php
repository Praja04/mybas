<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBerasPengambilansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beras_pengambilans', function (Blueprint $table) {
            $table->id();
            $table->string('id_stock')->nullable();
            $table->enum('Shift', [1, 2, 3]);
            $table->float('jumlah_pengambilan');
            $table->enum('status_approval', ['y', 'n'])->default('n');
            $table->date('approved_at')->nullable();
            $table->string('approved_by')->nullable();
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
        Schema::dropIfExists('beras_pengambilans');
    }
}
