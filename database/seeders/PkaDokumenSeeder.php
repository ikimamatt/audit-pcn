<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Models\Audit\ProgramKerjaAudit;

class PkaDokumenSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua ID dari program kerja audit yang sudah ada
        $programKerjaAuditList = ProgramKerjaAudit::all();
        
        if ($programKerjaAuditList->isEmpty()) {
            $this->command->warn('Tidak ada data program kerja audit. Skipping PkaDokumenSeeder.');
            return;
        }

        $dokumenData = [];
        
        foreach ($programKerjaAuditList as $index => $pka) {
            // Define dokumen data for each PKA (3 dokumen per PKA)
            $dokumens = [
                [
                    'nama_dokumen' => 'Program Kerja Audit ' . ($index + 1),
                    'file_path' => 'dokumen/pka_' . ($index + 1) . '.pdf',
                    'status_approval' => 'approved',
                    'approved_by' => 1,
                    'approved_at' => now(),
                ],
                [
                    'nama_dokumen' => 'Surat Tugas Audit ' . ($index + 1),
                    'file_path' => 'dokumen/surat_tugas_' . ($index + 1) . '.pdf',
                    'status_approval' => 'approved',
                    'approved_by' => 1,
                    'approved_at' => now(),
                ],
                [
                    'nama_dokumen' => 'Lampiran Dokumen ' . ($index + 1),
                    'file_path' => 'dokumen/lampiran_' . ($index + 1) . '.pdf',
                    'status_approval' => 'pending',
                    'approved_by' => null,
                    'approved_at' => null,
                ],
            ];

            foreach ($dokumens as $dokumen) {
                $dokumenData[] = [
                    'program_kerja_audit_id' => $pka->id,
                    'nama_dokumen' => $dokumen['nama_dokumen'],
                    'file_path' => $dokumen['file_path'],
                    'status_approval' => $dokumen['status_approval'],
                    'approved_by' => $dokumen['approved_by'],
                    'approved_at' => $dokumen['approved_at'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert all dokumen data
        if (!empty($dokumenData)) {
            DB::table('pka_dokumen')->insert($dokumenData);
            $this->command->info('PKA Dokumen seeder berhasil dijalankan.');
        }
    }
} 