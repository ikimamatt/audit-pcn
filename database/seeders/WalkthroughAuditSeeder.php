<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\MasterData\MasterAuditee;

class WalkthroughAuditSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil Program Kerja Audit yang memiliki milestone Walkthrough
        $programKerjaAuditList = ProgramKerjaAudit::whereHas('milestones', function($query) {
            $query->where('nama_milestone', 'Walkthrough');
        })->with(['milestones' => function($query) {
            $query->where('nama_milestone', 'Walkthrough');
        }, 'perencanaanAudit'])->get();
        
        if ($programKerjaAuditList->isEmpty()) {
            $this->command->warn('Tidak ada data program kerja audit dengan milestone Walkthrough. Skipping WalkthroughAuditSeeder.');
            return;
        }

        // Ambil data master auditee
        $auditees = MasterAuditee::all();
        
        if ($auditees->isEmpty()) {
            $this->command->warn('Tidak ada data master auditee. Skipping WalkthroughAuditSeeder.');
            return;
        }

        $walkthroughData = [];
        
        foreach ($programKerjaAuditList as $index => $pka) {
            // Ambil milestone Walkthrough untuk PKA ini
            $walkthroughMilestone = $pka->milestones->first();
            
            if (!$walkthroughMilestone) {
                continue; // Skip jika tidak ada milestone Walkthrough
            }

            // Pilih auditee secara random
            $randomAuditee = $auditees->random();
            
            // Generate actual date yang mungkin berbeda dari planned date
            $plannedDate = $walkthroughMilestone->tanggal_mulai;
            $actualDate = null;
            
            // 70% kemungkinan ada actual date
            if (rand(1, 100) <= 70) {
                // Actual date bisa sama atau berbeda dengan planned date
                $dateOffset = rand(-2, 3); // Bisa 2 hari sebelum atau 3 hari setelah planned date
                $actualDate = date('Y-m-d', strtotime($plannedDate . ' + ' . $dateOffset . ' days'));
            }

            // Generate status approval dengan distribusi yang realistis
            $statusOptions = ['pending', 'approved', 'rejected'];
            $statusWeights = [40, 40, 20]; // 40% pending, 40% approved, 20% rejected
            $randomStatus = $this->getRandomStatus($statusOptions, $statusWeights);

            // Generate hasil walkthrough yang realistis
            $hasilWalkthrough = $this->generateHasilWalkthrough($pka->perencanaanAudit->jenis_audit ?? 'Audit Operasional');

            // Generate rejection reason if status is rejected
            $rejectionReason = null;
            if ($randomStatus === 'rejected') {
                $rejectionReasons = [
                    'Dokumen pendukung tidak lengkap dan perlu dilengkapi terlebih dahulu sebelum walkthrough dapat dilaksanakan.',
                    'Jadwal walkthrough tidak sesuai dengan ketersediaan tim audit dan perlu dijadwalkan ulang.',
                    'Lokasi walkthrough tidak dapat diakses pada waktu yang direncanakan, perlu koordinasi ulang.',
                    'Auditee tidak dapat hadir pada waktu yang ditentukan, walkthrough perlu ditunda.',
                    'Dokumen SOP yang akan di-review belum tersedia, walkthrough perlu menunggu kelengkapan dokumen.',
                ];
                $rejectionReason = $rejectionReasons[array_rand($rejectionReasons)];
            }

            $walkthroughData[] = [
                'perencanaan_audit_id' => $pka->perencanaan_audit_id,
                'program_kerja_audit_id' => $pka->id,
                'planned_walkthrough_date' => $plannedDate,
                'actual_walkthrough_date' => $actualDate,
                'tanggal_walkthrough' => $actualDate ?? $plannedDate,
                'auditee_nama' => $randomAuditee->divisi,
                'hasil_walkthrough' => $hasilWalkthrough,
                'status_approval' => $randomStatus,
                'approved_by' => $randomStatus === 'approved' ? 1 : null,
                'approved_at' => $randomStatus === 'approved' ? now() : null,
                'rejection_reason' => $rejectionReason,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert all walkthrough data
        if (!empty($walkthroughData)) {
            DB::table('walkthrough_audit')->insert($walkthroughData);
            $this->command->info('Walkthrough Audit seeder berhasil dijalankan dengan ' . count($walkthroughData) . ' data.');
        }
    }

    /**
     * Get random status based on weights
     */
    private function getRandomStatus($options, $weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($options as $index => $option) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $option;
            }
        }
        
        return $options[0]; // Default to first option
    }

    /**
     * Generate realistic hasil walkthrough based on audit type
     */
    private function generateHasilWalkthrough($jenisAudit)
    {
        $hasilTemplates = [
            'Audit Operasional' => [
                'Walkthrough telah dilaksanakan untuk memahami proses operasional. Ditemukan beberapa area yang memerlukan perhatian khusus dalam hal efisiensi dan kepatuhan SOP.',
                'Hasil walkthrough menunjukkan bahwa proses operasional berjalan sesuai dengan standar yang ditetapkan. Beberapa rekomendasi perbaikan telah diidentifikasi.',
                'Walkthrough mengungkapkan beberapa ketidaksesuaian dalam implementasi prosedur operasional. Perlu dilakukan perbaikan untuk meningkatkan efektivitas.',
            ],
            'Audit Khusus' => [
                'Walkthrough untuk audit khusus telah selesai dilaksanakan. Fokus pada area spesifik yang menjadi tujuan audit menunjukkan beberapa temuan penting.',
                'Hasil walkthrough audit khusus menunjukkan bahwa area yang diaudit telah memenuhi kriteria yang ditetapkan. Beberapa catatan perbaikan minor telah diidentifikasi.',
                'Walkthrough audit khusus mengungkapkan beberapa temuan yang memerlukan tindak lanjut serius. Rekomendasi perbaikan telah disusun.',
            ],
            'Konsultasi' => [
                'Walkthrough konsultasi telah dilaksanakan untuk memberikan pemahaman mendalam tentang proses yang dikonsultasikan. Beberapa saran perbaikan telah disampaikan.',
                'Hasil walkthrough konsultasi menunjukkan bahwa proses yang dikonsultasikan berjalan dengan baik. Beberapa rekomendasi optimasi telah diidentifikasi.',
                'Walkthrough konsultasi mengungkapkan beberapa area yang dapat ditingkatkan. Saran perbaikan telah disusun untuk meningkatkan efektivitas.',
            ],
        ];

        $template = $hasilTemplates[$jenisAudit] ?? $hasilTemplates['Audit Operasional'];
        return $template[array_rand($template)];
    }
} 