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
        // Check if nomor_iss column exists before modifying it
        if (Schema::hasColumn('pelaporan_hasil_audit', 'nomor_iss')) {
            Schema::table('pelaporan_hasil_audit', function (Blueprint $table) {
                // Ubah field nomor_iss menjadi nullable karena data ISS sekarang disimpan di tabel terpisah
                $table->string('nomor_iss')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if nomor_iss column exists before modifying it
        if (Schema::hasColumn('pelaporan_hasil_audit', 'nomor_iss')) {
            Schema::table('pelaporan_hasil_audit', function (Blueprint $table) {
                // Kembalikan field nomor_iss menjadi required
                $table->string('nomor_iss')->nullable(false)->change();
            });
        }
    }
};
