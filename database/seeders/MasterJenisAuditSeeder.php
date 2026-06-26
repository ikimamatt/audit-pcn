<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterJenisAuditSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_jenis_audit')->delete();
        $data = [
            ['nama_jenis_audit' => 'Audit Operasional', 'kode' => 'SPI.01.02', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis_audit' => 'Audit Khusus', 'kode' => 'SPI.01.03', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis_audit' => 'Konsultasi', 'kode' => 'SPI.01.04', 'created_at' => now(), 'updated_at' => now()],
        ];
        foreach ($data as &$row) {
            $row['id'] = (string) \Illuminate\Support\Str::uuid();
        }
        DB::table('master_jenis_audit')->insert($data);
    }
}

