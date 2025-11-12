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
        Schema::table('penutup_lha_rekomendasi', function (Blueprint $table) {
            // Check if alasan_reject column doesn't exist before adding
            if (!Schema::hasColumn('penutup_lha_rekomendasi', 'alasan_reject')) {
                $table->text('alasan_reject')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penutup_lha_rekomendasi', function (Blueprint $table) {
            if (Schema::hasColumn('penutup_lha_rekomendasi', 'alasan_reject')) {
                $table->dropColumn('alasan_reject');
            }
        });
    }
};
