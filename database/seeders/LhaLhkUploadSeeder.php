<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LhaLhkUpload;
use Carbon\Carbon;

class LhaLhkUploadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleData = [
            [
                'nomor_lha_lhk' => 'LHA-001/AUDIT/2024',
                'tanggal_lha_lhk' => '2024-01-20',
                'jenis_audit' => 'AUDIT KEUANGAN',
                'auditee' => 'Divisi Keuangan',
                'periode_audit' => 'Januari - Desember 2024',
                'file_path' => 'uploads/lha-lhk/lha-001-audit-2024.pdf',
                'keterangan' => 'Laporan hasil audit keuangan tahun 2024',
                'status' => 'approved',
                'uploaded_by' => 1,
                'approved_by' => 1,
                'approved_at' => Carbon::now()->subDays(5),
                'approval_notes' => 'Dokumen lengkap dan sesuai standar audit',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'nomor_lha_lhk' => 'LHA-002/AUDIT/2024',
                'tanggal_lha_lhk' => '2024-02-25',
                'jenis_audit' => 'AUDIT OPERASIONAL',
                'auditee' => 'Divisi Operasional',
                'periode_audit' => 'Januari - Juni 2024',
                'file_path' => 'uploads/lha-lhk/lha-002-audit-2024.pdf',
                'keterangan' => 'Laporan hasil audit operasional semester I 2024',
                'status' => 'pending',
                'uploaded_by' => 1,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'nomor_lha_lhk' => 'LHA-003/AUDIT/2024',
                'tanggal_lha_lhk' => '2024-03-15',
                'jenis_audit' => 'AUDIT COMPLIANCE',
                'auditee' => 'Divisi Hukum & Compliance',
                'periode_audit' => 'Januari - Maret 2024',
                'file_path' => 'uploads/lha-lhk/lha-003-audit-2024.pdf',
                'keterangan' => 'Laporan hasil audit compliance Q1 2024',
                'status' => 'approved',
                'uploaded_by' => 1,
                'approved_by' => 1,
                'approved_at' => Carbon::now()->subDays(1),
                'approval_notes' => 'Dokumen sudah sesuai standar audit compliance',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'nomor_lha_lhk' => 'LHA-004/AUDIT/2024',
                'tanggal_lha_lhk' => '2024-04-10',
                'jenis_audit' => 'AUDIT IT',
                'auditee' => 'Divisi IT',
                'periode_audit' => 'Januari - April 2024',
                'file_path' => 'uploads/lha-lhk/lha-004-audit-2024.pdf',
                'keterangan' => 'Laporan hasil audit IT Q1 2024',
                'status' => 'pending',
                'uploaded_by' => 1,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'nomor_lha_lhk' => 'LHA-005/AUDIT/2024',
                'tanggal_lha_lhk' => '2024-05-18',
                'jenis_audit' => 'AUDIT RISIKO',
                'auditee' => 'Divisi Manajemen Risiko',
                'periode_audit' => 'Januari - Mei 2024',
                'file_path' => 'uploads/lha-lhk/lha-005-audit-2024.pdf',
                'keterangan' => 'Laporan hasil audit risiko tahun 2024',
                'status' => 'rejected',
                'uploaded_by' => 1,
                'approved_by' => 1,
                'approved_at' => Carbon::now()->subHours(6),
                'approval_notes' => 'Dokumen perlu perbaikan format dan konten',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subHours(6),
            ],
        ];

        foreach ($sampleData as $data) {
            LhaLhkUpload::create($data);
        }

        $this->command->info('LHA/LHK Upload seeder completed successfully!');
    }
} 