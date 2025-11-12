<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExitMeetingUpload;
use Carbon\Carbon;

class ExitMeetingUploadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleData = [
            [
                'nomor_exit_meeting' => 'EM-001/AUDIT/2024',
                'tanggal_exit_meeting' => '2024-01-25',
                'jenis_audit' => 'AUDIT KEUANGAN',
                'auditee' => 'Divisi Keuangan',
                'periode_audit' => 'Januari - Desember 2024',
                'temuan_audit' => 5,
                'rekomendasi' => 8,
                'file_path' => 'uploads/exit-meeting/em-001-audit-2024.pdf',
                'keterangan' => 'Exit meeting untuk audit keuangan tahun 2024',
                'status' => 'approved',
                'uploaded_by' => 1,
                'approved_by' => 1,
                'approved_at' => Carbon::now()->subDays(5),
                'approval_notes' => 'Dokumen lengkap dan sesuai prosedur',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'nomor_exit_meeting' => 'EM-002/AUDIT/2024',
                'tanggal_exit_meeting' => '2024-02-28',
                'jenis_audit' => 'AUDIT OPERASIONAL',
                'auditee' => 'Divisi Operasional',
                'periode_audit' => 'Januari - Juni 2024',
                'temuan_audit' => 3,
                'rekomendasi' => 6,
                'file_path' => 'uploads/exit-meeting/em-002-audit-2024.pdf',
                'keterangan' => 'Exit meeting untuk audit operasional semester I 2024',
                'status' => 'pending',
                'uploaded_by' => 1,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'nomor_exit_meeting' => 'EM-003/AUDIT/2024',
                'tanggal_exit_meeting' => '2024-03-18',
                'jenis_audit' => 'AUDIT COMPLIANCE',
                'auditee' => 'Divisi Hukum & Compliance',
                'periode_audit' => 'Januari - Maret 2024',
                'temuan_audit' => 2,
                'rekomendasi' => 4,
                'file_path' => 'uploads/exit-meeting/em-003-audit-2024.pdf',
                'keterangan' => 'Exit meeting untuk audit compliance Q1 2024',
                'status' => 'approved',
                'uploaded_by' => 1,
                'approved_by' => 1,
                'approved_at' => Carbon::now()->subDays(1),
                'approval_notes' => 'Dokumen sudah sesuai standar audit compliance',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'nomor_exit_meeting' => 'EM-004/AUDIT/2024',
                'tanggal_exit_meeting' => '2024-04-12',
                'jenis_audit' => 'AUDIT IT',
                'auditee' => 'Divisi IT',
                'periode_audit' => 'Januari - April 2024',
                'temuan_audit' => 4,
                'rekomendasi' => 7,
                'file_path' => 'uploads/exit-meeting/em-004-audit-2024.pdf',
                'keterangan' => 'Exit meeting untuk audit IT Q1 2024',
                'status' => 'pending',
                'uploaded_by' => 1,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'nomor_exit_meeting' => 'EM-005/AUDIT/2024',
                'tanggal_exit_meeting' => '2024-05-20',
                'jenis_audit' => 'AUDIT RISIKO',
                'auditee' => 'Divisi Manajemen Risiko',
                'periode_audit' => 'Januari - Mei 2024',
                'temuan_audit' => 6,
                'rekomendasi' => 10,
                'file_path' => 'uploads/exit-meeting/em-005-audit-2024.pdf',
                'keterangan' => 'Exit meeting untuk audit risiko tahun 2024',
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
            ExitMeetingUpload::create($data);
        }

        $this->command->info('Exit Meeting Upload seeder completed successfully!');
    }
} 