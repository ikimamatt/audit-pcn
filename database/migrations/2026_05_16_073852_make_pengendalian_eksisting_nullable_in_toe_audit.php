<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat kolom pengendalian_eksisting menjadi nullable di tabel toe_audit.
     * Kolom ini akan digantikan oleh relasi pivot ke pka_kontrol (toe_kontrol),
     * namun kolom tetap dipertahankan untuk backward compatibility dengan data lama.
     */
    public function up(): void
    {
        Schema::table('toe_audit', function (Blueprint $table) {
            $table->text('pengendalian_eksisting')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('toe_audit', function (Blueprint $table) {
            $table->text('pengendalian_eksisting')->nullable(false)->change();
        });
    }
};
