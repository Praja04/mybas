<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileKonselingToSpPelanggaransTable extends Migration
{
    public function up()
    {
        Schema::table('sp_pelanggarans', function (Blueprint $table) {
            if (!Schema::hasColumn('sp_pelanggarans', 'file_konseling')) {
                $table->string('file_konseling')->nullable()->after('lampiran_cancel');
            }
            if (!Schema::hasColumn('sp_pelanggarans', 'uploaded_konseling_at')) {
                $table->timestamp('uploaded_konseling_at')->nullable()->after('file_konseling');
            }
            if (!Schema::hasColumn('sp_pelanggarans', 'uploaded_konseling_by')) {
                $table->unsignedBigInteger('uploaded_konseling_by')->nullable()->after('uploaded_konseling_at');
            }
        });
    }

    public function down()
    {
        Schema::table('sp_pelanggarans', function (Blueprint $table) {
            if (Schema::hasColumn('sp_pelanggarans', 'file_konseling')) {
                $table->dropColumn(['file_konseling', 'uploaded_konseling_at', 'uploaded_konseling_by']);
            }
        });
    }
}
