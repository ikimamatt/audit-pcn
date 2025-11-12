<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Models\Audit\ProgramKerjaAudit;

class PkaRiskBasedAuditSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua ID dari program kerja audit yang sudah ada
        $programKerjaAuditList = ProgramKerjaAudit::all();
        
        if ($programKerjaAuditList->isEmpty()) {
            $this->command->warn('Tidak ada data program kerja audit. Skipping PkaRiskBasedAuditSeeder.');
            return;
        }

        $riskData = [];
        
        foreach ($programKerjaAuditList as $index => $pka) {
            // Define risk-based audit data for each PKA (3 risks per PKA)
            $risks = [
                [
                    'deskripsi_resiko' => 'Risiko ketidakpatuhan terhadap regulasi',
                    'penyebab_resiko' => 'Perubahan regulasi yang tidak diikuti dengan baik dan kurangnya pemahaman terhadap regulasi baru',
                    'dampak_resiko' => 'Sanksi dari regulator, kerugian finansial, dan kerusakan reputasi',
                    'pengendalian_eksisting' => 'Sistem monitoring regulasi, pelatihan berkala, dan review kepatuhan',
                ],
                [
                    'deskripsi_resiko' => 'Risiko inefisiensi operasional',
                    'penyebab_resiko' => 'Proses bisnis yang tidak optimal dan kurangnya standardisasi',
                    'dampak_resiko' => 'Peningkatan biaya operasional, penurunan produktivitas, dan kehilangan peluang',
                    'pengendalian_eksisting' => 'Review proses berkala, implementasi best practices, dan continuous improvement',
                ],
                [
                    'deskripsi_resiko' => 'Risiko kegagalan teknologi',
                    'penyebab_resiko' => 'Sistem IT yang tidak handal dan kurangnya maintenance',
                    'dampak_resiko' => 'Gangguan layanan, kehilangan data, dan kerugian finansial',
                    'pengendalian_eksisting' => 'Backup sistem, disaster recovery plan, dan monitoring sistem',
                ],
            ];

            foreach ($risks as $risk) {
                $riskData[] = [
                    'program_kerja_audit_id' => $pka->id,
                    'deskripsi_resiko' => $risk['deskripsi_resiko'],
                    'penyebab_resiko' => $risk['penyebab_resiko'],
                    'dampak_resiko' => $risk['dampak_resiko'],
                    'pengendalian_eksisting' => $risk['pengendalian_eksisting'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert all risk data
        if (!empty($riskData)) {
            DB::table('pka_risk_based_audit')->insert($riskData);
            $this->command->info('PKA Risk-Based Audit seeder berhasil dijalankan.');
        }
    }
} 