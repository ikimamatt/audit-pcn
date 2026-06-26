<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAksesUserSeeder extends Seeder
{
    public function run(): void
    {
        $aksesList = [
            'KSPI',
            'ASMAN SPI',
            'AUDITOR',
            'AUDITEE',
            'SUPER ADMIN',
            'VIEW BOD',
        ];

        foreach ($aksesList as $akses) {
            if (!DB::table('master_akses_user')->where('nama_akses', $akses)->exists()) {
                DB::table('master_akses_user')->insert([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'nama_akses' => $akses,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        echo "✅ MasterAksesUser seeder berhasil dijalankan (6 role baku)." . PHP_EOL;
    }
}