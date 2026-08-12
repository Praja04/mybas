<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpDigitalSignaturesTable extends Migration
{
    public function up()
    {
        Schema::create('sp_digital_signatures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('FK ke tabel users');
            $table->string('role', 30)->comment('dept_head / ir_head / ir_staff');
            $table->string('nama_jabatan', 150)->nullable()->comment('Nama jabatan untuk ditampilkan di PDF, misal: IR & ER Dept. Head');
            $table->string('signature_path')->nullable()->comment('Path file gambar TTD di storage/public');
            $table->boolean('is_active')->default(true)->comment('Hanya 1 TTD aktif per user');
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();

            // Tidak pakai FK constraint karena beda engine dengan tabel users
            $table->unique('user_id');
            $table->index('role');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sp_digital_signatures');
    }
}
