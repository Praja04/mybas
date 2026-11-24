<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrPmsPascoreFinalTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_pms_pascore_final')) {
            Schema::create('hr_pms_pascore_final', function (Blueprint $table) {
                $table->id();
                $table->string('periode', 10);
                $table->string('nik', 50);
                // Ciphertext AES-256-CBC (prefix pmsenc:v1:), bukan grade 1 karakter.
                $table->text('pa_scale_final')->nullable();
                $table->string('updated_by', 100)->nullable();
                $table->timestamps();

                $table->unique(['periode', 'nik'], 'hr_pms_pascore_final_periode_nik_uq');
                $table->index('nik', 'hr_pms_pascore_final_nik_idx');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('hr_pms_pascore_final')) {
            Schema::dropIfExists('hr_pms_pascore_final');
        }
    }
}
