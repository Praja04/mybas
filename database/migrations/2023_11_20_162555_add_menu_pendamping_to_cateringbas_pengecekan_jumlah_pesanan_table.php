<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMenuPendampingToCateringbasPengecekanJumlahPesananTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cateringbas_pengecekan_jumlah_pesanan', function (Blueprint $table) {
            $table->string('nama_makanan_pendamping')->nullable();
            $table->integer('qty_menu_makanan')->nullable();
            $table->integer('qty_menu_makanan_pendamping')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cateringbas_pengecekan_jumlah_pesanan', function (Blueprint $table) {
            $table->dropColumn('nama_makanan_pendamping');
            $table->dropColumn('qty_menu_makanan');
            $table->dropColumn('qty_menu_makanan_pendamping');
        });
    }
}
