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
        Schema::table('pelaporan_hasil_audit', function (Blueprint $table) {
            $table->unsignedInteger('nomor_urut')->after('id')->comment('Nomor urut untuk generate nomor LHA/LHK');
            $table->year('tahun')->after('nomor_urut')->comment('Tahun untuk generate nomor LHA/LHK');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelaporan_hasil_audit', function (Blueprint $table) {
            $table->dropColumn(['nomor_urut', 'tahun']);
        });
    }
};
