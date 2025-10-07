<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalColumnToPengirimCateringbasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengirim_cateringbas', function (Blueprint $table) {
            $table->enum('approval_cek_kendaraan', ['Y', 'N'])->default('N');
            $table->dateTime('approval_cek_kendaraan_at')->nullable();
            $table->string('approval_cek_kendaraan_by')->nullable();
            $table->enum('approval_cek_pesanan', ['Y', 'N'])->default('N');
            $table->dateTime('approval_cek_pesanan_at')->nullable();
            $table->string('approval_cek_pesanan_by')->nullable();
            $table->enum('approval_cek_sampel', ['Y', 'N'])->default('N');
            $table->dateTime('approval_cek_sampel_at')->nullable();
            $table->string('approval_cek_sampel_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengirim_cateringbas', function (Blueprint $table) {
            $table->enum('approval_cek_kendaraan', ['Y', 'N'])->default('N');
            $table->dateTime('approval_cek_kendaraan_at')->nullable();
            $table->string('approval_cek_kendaraan_by')->nullable();
            $table->enum('approval_cek_pesanan', ['Y', 'N'])->default('N');
            $table->dateTime('approval_cek_pesanan_at')->nullable();
            $table->string('approval_cek_pesanan_by')->nullable();
            $table->enum('approval_cek_sampel', ['Y', 'N'])->default('N');
            $table->dateTime('approval_cek_sampel_at')->nullable();
            $table->string('approval_cek_sampel_by')->nullable();
        });
    }
}
