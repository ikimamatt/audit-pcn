<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerencanaanAuditSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = date('Y');
        
        DB::table('perencanaan_audit')->insert([
            [
                'tanggal_surat_tugas' => '2024-07-01',
                'nomor_surat_tugas' => "001.STG/SPI.01.02/SPI-PCN/{$tahun}",
                'jenis_audit' => 'Audit Operasional',
                'auditor' => json_encode(['Auditor 1 - NIP: 123456789']),
                'auditee_id' => 1,
                'ruang_lingkup' => json_encode(['Sistem Keuangan', 'Sistem SDM']),
                'tanggal_audit_mulai' => '2024-07-10',
                'tanggal_audit_sampai' => '2024-07-15',
                'periode_audit' => 'Januari 2024 s/d Juni 2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tanggal_surat_tugas' => '2024-07-02',
                'nomor_surat_tugas' => "002.STG/SPI.01.02/SPI-PCN/{$tahun}",
                'jenis_audit' => 'Audit Operasional',
                'auditor' => json_encode(['Auditor 2 - NIP: 987654321']),
                'auditee_id' => 2,
                'ruang_lingkup' => json_encode(['Sistem Operasional', 'Sistem IT']),
                'tanggal_audit_mulai' => '2024-07-20',
                'tanggal_audit_sampai' => '2024-07-25',
                'periode_audit' => 'Januari 2024 s/d Juni 2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tanggal_surat_tugas' => '2024-07-03',
                'nomor_surat_tugas' => "001.STG/SPI.01.03/SPI-PCN/{$tahun}",
                'jenis_audit' => 'Audit Khusus',
                'auditor' => json_encode(['Auditor 3 - NIP: 456789123']),
                'auditee_id' => 3,
                'ruang_lingkup' => json_encode(['Investigasi Khusus', 'Pemeriksaan Khusus']),
                'tanggal_audit_mulai' => '2024-08-01',
                'tanggal_audit_sampai' => '2024-08-05',
                'periode_audit' => 'Januari 2024 s/d Desember 2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tanggal_surat_tugas' => '2024-07-04',
                'nomor_surat_tugas' => "001.STG/SPI.01.04/SPI-PCN/{$tahun}",
                'jenis_audit' => 'Konsultasi',
                'auditor' => json_encode(['Konsultan 1 - NIP: 789123456']),
                'auditee_id' => 4,
                'ruang_lingkup' => json_encode(['Konsultasi Sistem', 'Konsultasi Proses']),
                'tanggal_audit_mulai' => '2024-08-10',
                'tanggal_audit_sampai' => '2024-08-15',
                'periode_audit' => 'Januari 2024 s/d Desember 2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tanggal_surat_tugas' => '2024-07-05',
                'nomor_surat_tugas' => "003.STG/SPI.01.02/SPI-PCN/{$tahun}",
                'jenis_audit' => 'Audit Operasional',
                'auditor' => json_encode(['Auditor 4 - NIP: 321654987', 'Auditor 5 - NIP: 654987321']),
                'auditee_id' => 5,
                'ruang_lingkup' => json_encode(['Sistem Keamanan', 'Sistem Monitoring', 'Sistem Pelaporan']),
                'tanggal_audit_mulai' => '2024-08-20',
                'tanggal_audit_sampai' => '2024-08-30',
                'periode_audit' => 'Januari 2024 s/d Juni 2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tanggal_surat_tugas' => '2024-07-06',
                'nomor_surat_tugas' => "004.STG/SPI.01.02/SPI-PCN/{$tahun}",
                'jenis_audit' => 'Audit Kepatuhan',
                'auditor' => json_encode(['Auditor 6 - NIP: 147258369']),
                'auditee_id' => 1,
                'ruang_lingkup' => json_encode(['Kepatuhan Regulasi', 'Sistem Pengendalian']),
                'tanggal_audit_mulai' => '2024-09-01',
                'tanggal_audit_sampai' => '2024-09-10',
                'periode_audit' => 'Januari 2024 s/d Desember 2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tanggal_surat_tugas' => '2024-07-07',
                'nomor_surat_tugas' => "005.STG/SPI.01.02/SPI-PCN/{$tahun}",
                'jenis_audit' => 'Audit Sistem Informasi',
                'auditor' => json_encode(['Auditor 7 - NIP: 963852741', 'Auditor 8 - NIP: 852963741']),
                'auditee_id' => 2,
                'ruang_lingkup' => json_encode(['Sistem IT', 'Keamanan Data', 'Infrastruktur']),
                'tanggal_audit_mulai' => '2024-09-15',
                'tanggal_audit_sampai' => '2024-09-25',
                'periode_audit' => 'Januari 2024 s/d Desember 2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tanggal_surat_tugas' => '2024-07-08',
                'nomor_surat_tugas' => "006.STG/SPI.01.02/SPI-PCN/{$tahun}",
                'jenis_audit' => 'Audit Keuangan',
                'auditor' => json_encode(['Auditor 9 - NIP: 741852963']),
                'auditee_id' => 3,
                'ruang_lingkup' => json_encode(['Laporan Keuangan', 'Sistem Akuntansi', 'Pengendalian Internal']),
                'tanggal_audit_mulai' => '2024-10-01',
                'tanggal_audit_sampai' => '2024-10-15',
                'periode_audit' => 'Januari 2024 s/d Desember 2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 