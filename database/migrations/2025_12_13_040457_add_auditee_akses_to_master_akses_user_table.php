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
        // Tambahkan akses "Auditee" ke tabel master_akses_user
        DB::table('master_akses_user')->updateOrInsert(
            ['nama_akses' => 'Auditee'],
            ['nama_akses' => 'Auditee']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus akses "Auditee" dari tabel master_akses_user
        DB::table('master_akses_user')->where('nama_akses', 'Auditee')->delete();
    }
};
