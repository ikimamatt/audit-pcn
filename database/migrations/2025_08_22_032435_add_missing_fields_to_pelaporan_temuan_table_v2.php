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
        Schema::table('pelaporan_temuan', function (Blueprint $table) {
            // Add missing fields that are used in the controller but not in migrations
            if (!Schema::hasColumn('pelaporan_temuan', 'penyebab')) {
                $table->text('penyebab')->nullable()->after('permasalahan');
            }
            if (!Schema::hasColumn('pelaporan_temuan', 'alasan_reject')) {
                $table->text('alasan_reject')->nullable()->after('approved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelaporan_temuan', function (Blueprint $table) {
            // Remove the added fields
            if (Schema::hasColumn('pelaporan_temuan', 'penyebab')) {
                $table->dropColumn('penyebab');
            }
            if (Schema::hasColumn('pelaporan_temuan', 'alasan_reject')) {
                $table->dropColumn('alasan_reject');
            }
        });
    }
};
