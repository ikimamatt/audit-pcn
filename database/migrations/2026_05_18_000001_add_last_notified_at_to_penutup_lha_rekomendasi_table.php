<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penutup_lha_rekomendasi', function (Blueprint $table) {
            // Tracking kapan terakhir notifikasi dikirim (untuk mencegah spam)
            $table->timestamp('last_notified_at')->nullable()->after('status_tindak_lanjut');
        });
    }

    public function down(): void
    {
        Schema::table('penutup_lha_rekomendasi', function (Blueprint $table) {
            $table->dropColumn('last_notified_at');
        });
    }
};
