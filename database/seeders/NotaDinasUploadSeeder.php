<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NotaDinasUpload;
use Carbon\Carbon;

class NotaDinasUploadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleData = [
            [
                'nomor_nota_dinas' => 'ND-001/AUDIT/2024',
                'tanggal_nota_dinas' => '2024-01-15',
                'penerima' => json_encode(['DIRUT', 'DEKOM']),
                'file_path' => 'uploads/nota-dinas/nd-001-audit-2024.pdf',
                'keterangan' => 'Nota dinas untuk audit keuangan tahun 2024',
                'status' => 'approved',
                'uploaded_by' => 1,
                'approved_by' => 1,
                'approved_at' => Carbon::now()->subDays(5),
                'approval_notes' => 'Dokumen lengkap dan sesuai prosedur',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'nomor_nota_dinas' => 'ND-002/AUDIT/2024',
                'tanggal_nota_dinas' => '2024-02-20',
                'penerima' => json_encode(['AUDITEE', 'MANAJEMEN']),
                'file_path' => 'uploads/nota-dinas/nd-002-audit-2024.pdf',
                'keterangan' => 'Nota dinas untuk audit operasional',
                'status' => 'pending',
                'uploaded_by' => 1,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'nomor_nota_dinas' => 'ND-003/AUDIT/2024',
                'tanggal_nota_dinas' => '2024-03-10',
                'penerima' => json_encode(['DIRUT', 'AUDITEE']),
                'file_path' => 'uploads/nota-dinas/nd-003-audit-2024.pdf',
                'keterangan' => 'Nota dinas untuk audit compliance',
                'status' => 'approved',
                'uploaded_by' => 1,
                'approved_by' => 1,
                'approved_at' => Carbon::now()->subDays(1),
                'approval_notes' => 'Dokumen sudah sesuai standar audit',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'nomor_nota_dinas' => 'ND-004/AUDIT/2024',
                'tanggal_nota_dinas' => '2024-04-05',
                'penerima' => json_encode(['DEKOM', 'MANAJEMEN']),
                'file_path' => 'uploads/nota-dinas/nd-004-audit-2024.pdf',
                'keterangan' => 'Nota dinas untuk audit IT',
                'status' => 'pending',
                'uploaded_by' => 1,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'nomor_nota_dinas' => 'ND-005/AUDIT/2024',
                'tanggal_nota_dinas' => '2024-05-12',
                'penerima' => json_encode(['DIRUT', 'DEKOM', 'AUDITEE']),
                'file_path' => 'uploads/nota-dinas/nd-005-audit-2024.pdf',
                'keterangan' => 'Nota dinas untuk audit risiko',
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
            NotaDinasUpload::create($data);
        }

        $this->command->info('Nota Dinas Upload seeder completed successfully!');
    }
} 