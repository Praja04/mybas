<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePkwFormPaAspekPenilaianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkw_form_pa_aspek_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_form_pa');
            $table->foreignId('id_aspek_penilaian');
            $table->enum('skala', ['1','2','3']); // 1,2,3
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->foreign('id_form_pa')->references('id')->on('pkw_form_pa');
            $table->foreign('id_aspek_penilaian')->references('id')->on('pkw_aspek_penilaian');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pkw_form_pa_aspek_penilaian');
    }
}
