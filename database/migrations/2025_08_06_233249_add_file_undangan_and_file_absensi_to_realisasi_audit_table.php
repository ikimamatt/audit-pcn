<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('realisasi_audits', function (Blueprint $table) {
            $table->string('file_undangan')->nullable()->after('status');
            $table->string('file_absensi')->nullable()->after('file_undangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasi_audits', function (Blueprint $table) {
            $table->dropColumn(['file_undangan', 'file_absensi']);
        });
    }
};
