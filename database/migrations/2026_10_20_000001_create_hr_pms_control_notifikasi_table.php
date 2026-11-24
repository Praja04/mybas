<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateHrPmsControlNotifikasiTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hr_pms_control_notifikasi')) {
            Schema::create('hr_pms_control_notifikasi', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_active')->default(true);
                $table->string('updated_by', 100)->nullable();
                $table->timestamps();
            });

            DB::table('hr_pms_control_notifikasi')->insert([
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        if (Schema::hasTable('hr_pms_control_notifikasi')) {
            Schema::dropIfExists('hr_pms_control_notifikasi');
        }
    }
}
