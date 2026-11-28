<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParkingZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parking_zones', function (Blueprint $table) {
            $table->id();
            $table->string('kode_zona')->unique();
            $table->string('nama_zona');
            $table->integer('kapasitas_total')->default(0);
            $table->enum('status', ['aktif', 'non_aktif', 'maintenance'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('parking_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_zone_id')->constrained('parking_zones')->onDelete('cascade');
            $table->string('kode_slot');
            $table->string('jenis_kendaraan')->nullable();
            $table->enum('status_slot', ['kosong', 'terisi', 'reserved', 'maintenance', 'non_aktif'])->default('kosong');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['parking_zone_id', 'kode_slot']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parking_slots');
        Schema::dropIfExists('parking_zones');
    }
}
