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
        Schema::table('tod_bpm_audit', function (Blueprint $table) {
            $table->text('resiko')->nullable()->after('nama_bpo');
            $table->text('kontrol')->nullable()->after('resiko');
            $table->string('file_kka_tod')->nullable()->after('file_bpm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tod_bpm_audit', function (Blueprint $table) {
            $table->dropColumn(['resiko', 'kontrol', 'file_kka_tod']);
        });
    }
};
