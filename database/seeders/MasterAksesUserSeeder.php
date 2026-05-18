<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAksesUserSeeder extends Seeder
{
    public function run(): void
    {
        // 6 Role baku sistem RBAC
        $aksesList = [
            'KSPI',
            'ASMAN SPI',
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

        echo "✅ MasterAksesUser seeder berhasil dijalankan (6 role baku)." . PHP_EOL;
    }
}