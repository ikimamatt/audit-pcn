<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Audit\PerencanaanAudit;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\Models\Audit\PkaMilestone;
use App\Models\Models\Audit\PkaRiskBasedAudit;
use App\Models\Models\Audit\PkaDokumen;

class ProgramKerjaAuditSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua ID dari perencanaan audit yang sudah ada
        $perencanaanAuditList = PerencanaanAudit::all();
        
        if ($perencanaanAuditList->isEmpty()) {
            $this->command->warn('Tidak ada data perencanaan audit. Skipping ProgramKerjaAuditSeeder.');
            return;
        }

        $pkaData = [];
        $riskData = [];
        $dokumenData = [];
        
        foreach ($perencanaanAuditList as $index => $perencanaanAudit) {
            // Create PKA entry
            $pkaId = DB::table('program_kerja_audit')->insertGetId([
                'perencanaan_audit_id' => $perencanaanAudit->id,
                'tanggal_pka' => '2024-07-01',
                'no_pka' => 'PKA-00' . ($index + 1) . '/2024',
                'informasi_umum' => 'Program Kerja Audit untuk ' . $perencanaanAudit->jenis_audit . ' pada ' . $perencanaanAudit->auditee->direktorat ?? 'Direktorat',
                'kpi_tidak_tercapai' => 'KPI yang tidak tercapai dalam audit ' . ($index + 1) . ': Efisiensi operasional, Kepatuhan regulasi, dan Pengelolaan risiko',
                'data_awal_dokumen' => 'Data awal dokumen untuk audit ' . ($index + 1) . ': Laporan keuangan, SOP, dan Dokumen pendukung lainnya',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create risk-based audit data
            $risks = [
                [
                    'deskripsi_resiko' => 'Risiko ketidakpatuhan terhadap regulasi',
                    'penyebab_resiko' => 'Perubahan regulasi yang tidak diikuti dengan baik',
                    'dampak_resiko' => 'Sanksi dari regulator dan kerugian finansial',
                    'pengendalian_eksisting' => 'Sistem monitoring regulasi dan pelatihan berkala',
                ],
                [
                    'deskripsi_resiko' => 'Risiko inefisiensi operasional',
                    'penyebab_resiko' => 'Proses bisnis yang tidak optimal',
                    'dampak_resiko' => 'Peningkatan biaya operasional dan penurunan produktivitas',
                    'pengendalian_eksisting' => 'Review proses berkala dan implementasi best practices',
                ],
                [
                    'deskripsi_resiko' => 'Risiko kegagalan teknologi',
                    'penyebab_resiko' => 'Sistem IT yang tidak handal',
                    'dampak_resiko' => 'Gangguan layanan dan kehilangan data',
                    'pengendalian_eksisting' => 'Backup sistem dan disaster recovery plan',
                ],
            ];

            foreach ($risks as $risk) {
                $riskData[] = [
                    'program_kerja_audit_id' => $pkaId,
                    'deskripsi_resiko' => $risk['deskripsi_resiko'],
                    'penyebab_resiko' => $risk['penyebab_resiko'],
                    'dampak_resiko' => $risk['dampak_resiko'],
                    'pengendalian_eksisting' => $risk['pengendalian_eksisting'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Create dokumen data
            $dokumens = [
                [
                    'nama_dokumen' => 'Program Kerja Audit ' . ($index + 1),
                    'file_path' => 'dokumen/pka_' . ($index + 1) . '.pdf',
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
                [
                    'nama_dokumen' => 'Surat Tugas Audit ' . ($index + 1),
                    'file_path' => 'dokumen/surat_tugas_' . ($index + 1) . '.pdf',
                    'status_approval' => 'approved',
                    'approved_by' => 1,
                    'approved_at' => now(),
                ],
            ];

            foreach ($dokumens as $dokumen) {
                $dokumenData[] = [
                    'program_kerja_audit_id' => $pkaId,
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

        // Insert all risk data
        if (!empty($riskData)) {
            DB::table('pka_risk_based_audit')->insert($riskData);
        }

        // Insert all dokumen data
        if (!empty($dokumenData)) {
            DB::table('pka_dokumen')->insert($dokumenData);
        }

        $this->command->info('Program Kerja Audit seeder berhasil dijalankan dengan risks dan dokumen.');
    }
} 