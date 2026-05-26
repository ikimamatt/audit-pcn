<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAuditeeSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data dan reset auto-increment tanpa melanggar foreign key constraint
        DB::table('master_auditee')->delete();
        DB::statement('ALTER TABLE master_auditee AUTO_INCREMENT = 1');
        // Data bidang sesuai dengan master_bidang
        DB::table('master_auditee')->insert([
            ['kd_bidang' => '01', 'nama_bidang' => 'PEMBANGKITAN',                     'is_available_for_up' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['kd_bidang' => '02', 'nama_bidang' => 'DISTRIBUSI',                       'is_available_for_up' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['kd_bidang' => '03', 'nama_bidang' => 'PELAYANAN PELANGGAN',              'is_available_for_up' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['kd_bidang' => '04', 'nama_bidang' => 'TRANSMISI DAN GARDU INDUK',        'is_available_for_up' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['kd_bidang' => '05', 'nama_bidang' => 'SDM & UMUM',                       'is_available_for_up' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['kd_bidang' => '06', 'nama_bidang' => 'KEUANGAN & ANGGARAN',              'is_available_for_up' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['kd_bidang' => '07', 'nama_bidang' => 'SEKPER',                           'is_available_for_up' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['kd_bidang' => '08', 'nama_bidang' => 'PERENCANAAN & PENGEMBANGAN USAHA', 'is_available_for_up' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['kd_bidang' => '09', 'nama_bidang' => 'K3LH',                            'is_available_for_up' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['kd_bidang' => '10', 'nama_bidang' => 'SPI',                              'is_available_for_up' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['kd_bidang' => '11', 'nama_bidang' => 'BEYOND KWH',                       'is_available_for_up' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['kd_bidang' => '12', 'nama_bidang' => 'OPERASI',                          'is_available_for_up' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
