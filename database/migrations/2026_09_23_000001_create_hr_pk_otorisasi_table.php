<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrPkOtorisasiTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_pk_otorisasi')) {
            Schema::create('hr_pk_otorisasi', function (Blueprint $table) {
                $table->id();
                $table->string('nik_yang_bersangkutan', 50);
                $table->string('nama_yang_bersangkutan', 150)->nullable();
                $table->string('nik_manager', 50)->nullable();
                $table->string('nama_manager', 150)->nullable();
                $table->string('nik_associate_manager', 50)->nullable();
                $table->string('nama_associate_manager', 150)->nullable();
                $table->string('send_by_username', 100)->nullable();
                $table->integer('jumlah_bawahan')->nullable();
                $table->string('kategori', 20)->default('spv');
                $table->timestamps();

                $table->unique('nik_yang_bersangkutan', 'hr_pk_otorisasi_nik_uq');
                $table->index('kategori', 'hr_pk_otorisasi_kategori_idx');
            });
        }

        if (Schema::hasTable('hr_pk_otorisasi') && !Schema::hasColumn('hr_pk_otorisasi', 'kategori')) {
            Schema::table('hr_pk_otorisasi', function (Blueprint $table) {
                $table->string('kategori', 20)->default('spv')->after('send_by_username');
                $table->index('kategori', 'hr_pk_otorisasi_kategori_idx');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('hr_pk_otorisasi')) {
            Schema::dropIfExists('hr_pk_otorisasi');
        }
    }
}
