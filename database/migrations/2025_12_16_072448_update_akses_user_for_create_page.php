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
        // Tambahkan 6 akses user sesuai dengan gambar untuk halaman create user
        $aksesList = [
            'ASMAN SPI',
            'KSPI',
            'AUDITOR',
            'AUDITEE',
            'SUPER ADMIN',
            'VIEW BOD',
        ];

        foreach ($aksesList as $akses) {
            DB::table('master_akses_user')->updateOrInsert(
                ['nama_akses' => $akses],
                ['nama_akses' => $akses]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus akses yang ditambahkan (jika diperlukan)
        $aksesList = [
            'ASMAN SPI',
            'AUDITOR',
            'AUDITEE',
            'SUPER ADMIN',
            'VIEW BOD',
        ];

        DB::table('master_akses_user')->whereIn('nama_akses', $aksesList)->delete();
    }
};
