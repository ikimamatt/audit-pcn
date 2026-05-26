<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Kolom direktorat & divisi_cabang tidak lagi digunakan —
     * digantikan oleh kd_bidang, nama_bidang, is_available_for_up di migration awal.
     */
    public function up(): void
    {
        // no-op
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // no-op
    }
};
