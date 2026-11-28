<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParkingAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parking_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_zone_id')->constrained('parking_zones')->onDelete('cascade');
            $table->foreignId('parking_slot_id')->constrained('parking_slots')->onDelete('cascade');
            $table->string('no_polisi');
            $table->string('jenis_kendaraan')->nullable();
            $table->string('nama_driver')->nullable();
            $table->string('no_hp_driver')->nullable();
            $table->unsignedBigInteger('visitor_transaction_id')->nullable();
            $table->dateTime('waktu_masuk');
            $table->dateTime('waktu_keluar')->nullable();
            $table->enum('status_assignment', ['assigned', 'parked', 'completed', 'cancelled'])->default('assigned');
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parking_assignments');
    }
}
