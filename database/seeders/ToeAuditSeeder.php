<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Audit\PerencanaanAudit;

class ToeAuditSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua Program Kerja Audit yang sudah ada
        $programKerjaAudit = \App\Models\Models\Audit\ProgramKerjaAudit::with('perencanaanAudit')->get();

        if ($programKerjaAudit->isEmpty()) {
            $this->command->warn('Tidak ada data Program Kerja Audit. Skipping ToeAuditSeeder.');
            return;
        }

        $statusOptions = ['pending', 'approved', 'rejected'];
        $statusWeights = [40, 40, 20]; // 40% pending, 40% approved, 20% rejected

        $toeData = [];
        $evaluasiData = [];

        foreach ($programKerjaAudit as $index => $pka) {
            // Pilih status berdasarkan weight
            $randomStatus = $this->getRandomStatus($statusOptions, $statusWeights);
            
            // Generate rejection reason if status is rejected
            $rejectionReason = null;
            if ($randomStatus === 'rejected') {
                $rejectionReasons = [
                    'Dokumen TOE tidak lengkap dan perlu dilengkapi terlebih dahulu sebelum dapat diapprove.',
                    'Judul BPM dalam TOE tidak sesuai dengan scope audit yang direncanakan, perlu revisi.',
                    'Pengendalian eksisting yang diidentifikasi tidak sesuai dengan standar yang berlaku.',
                    'Evaluasi TOE menunjukkan hasil yang tidak memuaskan, perlu perbaikan.',
                    'Dokumentasi pengendalian internal tidak lengkap, perlu dilengkapi.',
                ];
                $rejectionReason = $rejectionReasons[array_rand($rejectionReasons)];
            }

            $data = [
                'perencanaan_audit_id' => $pka->perencanaan_audit_id,
                'judul_bpm' => 'Terms of Engagement untuk ' . $pka->perencanaanAudit->jenis_audit . ' ' . ($index + 1),
                'pengendalian_eksisting' => 'Pengendalian eksisting untuk ' . $pka->perencanaanAudit->jenis_audit . ' meliputi: SOP, monitoring berkala, dan review manajemen.',
                'status_approval' => $randomStatus,
                'rejection_reason' => $rejectionReason,
                'approved_by' => ($randomStatus === 'approved' || $randomStatus === 'rejected') ? 1 : null,
                'approved_at' => ($randomStatus === 'approved' || $randomStatus === 'rejected') ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $toeData[] = $data;
        }

        // Insert all TOE data
        if (!empty($toeData)) {
            DB::table('toe_audit')->insert($toeData);
            
            // Get inserted TOE IDs for evaluasi
            $toeIds = DB::table('toe_audit')->pluck('id')->toArray();
            
            // Create evaluasi data for each TOE
            foreach ($toeIds as $toeId) {
                $evaluasiItems = [
                    'Terms of Engagement sudah sesuai dengan standar audit yang berlaku',
                    'Scope audit sudah didefinisikan dengan jelas dan tepat',
                    'Timeline audit sudah disusun dengan realistis',
                    'Resource yang diperlukan sudah diidentifikasi dengan baik',
                    'Komunikasi dengan auditee sudah terjalin dengan baik',
                ];

                foreach ($evaluasiItems as $evaluasi) {
                    $evaluasiData[] = [
                        'toe_audit_id' => $toeId,
                        'hasil_evaluasi' => $evaluasi,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Insert evaluasi data
            if (!empty($evaluasiData)) {
                DB::table('toe_evaluasi')->insert($evaluasiData);
            }
        }

        $this->command->info('TOE seeder berhasil dijalankan dengan ' . count($toeData) . ' data dan status approval yang bervariasi.');
    }

    private function getRandomStatus($options, $weights)
    {
        $totalWeight = array_sum($weights);
        $random = mt_rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($options as $index => $option) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $option;
            }
        }
        
        return $options[0]; // fallback
    }
} 