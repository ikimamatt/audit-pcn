<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change kode_spi from enum to string
        Schema::table('pelaporan_hasil_audit', function (Blueprint $table) {
            // Drop the enum constraint first
            DB::statement("ALTER TABLE pelaporan_hasil_audit MODIFY COLUMN kode_spi VARCHAR(255) NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelaporan_hasil_audit', function (Blueprint $table) {
            // Change back to enum
            DB::statement("ALTER TABLE pelaporan_hasil_audit MODIFY COLUMN kode_spi ENUM('SPI.01.02', 'SPI.01.03', 'SPI.01.04') NOT NULL");
        });
    }
};

