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
        // Add alasan_reject field to pelaporan_temuan if it doesn't exist
        if (!Schema::hasColumn('pelaporan_temuan', 'alasan_reject')) {
            Schema::table('pelaporan_temuan', function (Blueprint $table) {
                $table->text('alasan_reject')->nullable()->after('approved_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove alasan_reject field from pelaporan_temuan
        if (Schema::hasColumn('pelaporan_temuan', 'alasan_reject')) {
            Schema::table('pelaporan_temuan', function (Blueprint $table) {
                $table->dropColumn('alasan_reject');
            });
        }
    }
};
