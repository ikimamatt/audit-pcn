<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Audit\PerencanaanAudit;

class TodBpmAuditSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua Program Kerja Audit yang sudah ada
        $programKerjaAudit = \App\Models\Models\Audit\ProgramKerjaAudit::with('perencanaanAudit')->get();

        if ($programKerjaAudit->isEmpty()) {
            $this->command->warn('Tidak ada data Program Kerja Audit. Skipping TodBpmAuditSeeder.');
            return;
        }

        $statusOptions = ['pending', 'approved', 'rejected'];
        $statusWeights = [40, 40, 20]; // 40% pending, 40% approved, 20% rejected

        $todBpmData = [];
        $evaluasiData = [];

        foreach ($programKerjaAudit as $index => $pka) {
            // Pilih status berdasarkan weight
            $randomStatus = $this->getRandomStatus($statusOptions, $statusWeights);
            
            // Generate rejection reason if status is rejected
            $rejectionReason = null;
            if ($randomStatus === 'rejected') {
                $rejectionReasons = [
                    'Dokumen BPM tidak lengkap dan perlu dilengkapi terlebih dahulu sebelum dapat diapprove.',
                    'Judul BPM tidak sesuai dengan scope audit yang direncanakan, perlu revisi.',
                    'Nama BPO yang disebutkan tidak sesuai dengan struktur organisasi yang ada.',
                    'File BPM yang diupload tidak dapat dibuka atau rusak, perlu upload ulang.',
                    'Evaluasi BPM menunjukkan hasil yang tidak memuaskan, perlu perbaikan.',
                ];
                $rejectionReason = $rejectionReasons[array_rand($rejectionReasons)];
            }

            $data = [
                'perencanaan_audit_id' => $pka->perencanaan_audit_id,
                'judul_bpm' => 'Business Process Mapping untuk ' . $pka->perencanaanAudit->jenis_audit . ' ' . ($index + 1),
                'nama_bpo' => 'BPO ' . ($index + 1) . ' - ' . ($pka->perencanaanAudit->auditee->direktorat ?? 'Direktorat'),
                'file_bpm' => 'bpm/bpm_' . ($index + 1) . '.pdf',
                'status_approval' => $randomStatus,
                'rejection_reason' => $rejectionReason,
                'approved_by' => ($randomStatus === 'approved' || $randomStatus === 'rejected') ? 1 : null,
                'approved_at' => ($randomStatus === 'approved' || $randomStatus === 'rejected') ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $todBpmData[] = $data;
        }

        // Insert all TOD BPM data
        if (!empty($todBpmData)) {
            DB::table('tod_bpm_audit')->insert($todBpmData);
            
            // Get inserted TOD BPM IDs for evaluasi
            $todBpmIds = DB::table('tod_bpm_audit')->pluck('id')->toArray();
            
            // Create evaluasi data for each TOD BPM
            foreach ($todBpmIds as $todBpmId) {
                $evaluasiItems = [
                    'Proses mapping sudah sesuai dengan standar yang berlaku',
                    'Dokumentasi proses lengkap dan mudah dipahami',
                    'Identifikasi risiko sudah dilakukan dengan baik',
                    'Pengendalian internal sudah teridentifikasi dengan jelas',
                    'Rekomendasi perbaikan sudah disusun dengan tepat',
                ];

                foreach ($evaluasiItems as $evaluasi) {
                    $evaluasiData[] = [
                        'tod_bpm_audit_id' => $todBpmId,
                        'hasil_evaluasi' => $evaluasi,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Insert evaluasi data
            if (!empty($evaluasiData)) {
                DB::table('tod_bpm_evaluasi')->insert($evaluasiData);
            }
        }

        $this->command->info('TOD BPM seeder berhasil dijalankan dengan ' . count($todBpmData) . ' data dan status approval yang bervariasi.');
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