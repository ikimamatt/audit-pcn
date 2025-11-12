<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\Audit\PerencanaanAudit;

class EntryMeetingSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua PKA yang sudah ada (sesuai dengan PkaAllSeeder)
        $programKerjaAudit = ProgramKerjaAudit::with(['perencanaanAudit', 'milestones' => function($query) {
            $query->where('nama_milestone', 'Entry Meeting');
        }])->get();

        if ($programKerjaAudit->isEmpty()) {
            $this->command->warn('Tidak ada data Program Kerja Audit. Skipping EntryMeetingSeeder.');
            return;
        }

        $statusOptions = ['pending', 'approved', 'rejected'];
        $statusWeights = [40, 40, 20]; // 40% pending, 40% approved, 20% rejected

        $entryMeetingData = [];

        foreach ($programKerjaAudit as $index => $pka) {
            $entryMeetingMilestone = $pka->milestones->where('nama_milestone', 'Entry Meeting')->first();
            
            if ($entryMeetingMilestone) {
                // Pilih status berdasarkan weight
                $randomStatus = $this->getRandomStatus($statusOptions, $statusWeights);
                
                // Generate rejection reason if status is rejected
                $rejectionReason = null;
                if ($randomStatus === 'rejected') {
                    $rejectionReasons = [
                        'Dokumen undangan tidak lengkap dan perlu dilengkapi terlebih dahulu sebelum entry meeting dapat dilaksanakan.',
                        'Jadwal entry meeting tidak sesuai dengan ketersediaan tim audit dan perlu dijadwalkan ulang.',
                        'Lokasi entry meeting tidak dapat diakses pada waktu yang direncanakan, perlu koordinasi ulang.',
                        'Auditee tidak dapat hadir pada waktu yang ditentukan, entry meeting perlu ditunda.',
                        'Dokumen absensi yang diperlukan belum tersedia, entry meeting perlu menunggu kelengkapan dokumen.',
                    ];
                    $rejectionReason = $rejectionReasons[array_rand($rejectionReasons)];
                }

                $data = [
                    'program_kerja_audit_id' => $pka->id,
                    'tanggal' => $entryMeetingMilestone->tanggal_mulai, // planned meeting date
                    'actual_meeting_date' => date('Y-m-d', strtotime($entryMeetingMilestone->tanggal_mulai . ' + ' . rand(0, 3) . ' days')), // actual meeting date (random 0-3 days after planned)
                    'auditee_id' => $pka->perencanaanAudit->auditee_id,
                    'file_undangan' => 'entry_meeting/undangan_' . ($index + 1) . '.pdf',
                    'file_absensi' => 'entry_meeting/absensi_' . ($index + 1) . '.pdf',
                    'status_approval' => $randomStatus,
                    'rejection_reason' => $rejectionReason,
                    'approved_by' => ($randomStatus === 'approved' || $randomStatus === 'rejected') ? 1 : null,
                    'approved_at' => ($randomStatus === 'approved' || $randomStatus === 'rejected') ? now() : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $entryMeetingData[] = $data;
            }
        }

        // Insert all entry meeting data
        if (!empty($entryMeetingData)) {
            DB::table('entry_meeting')->insert($entryMeetingData);
            $this->command->info('Entry Meeting seeder berhasil dijalankan dengan ' . count($entryMeetingData) . ' data dan status approval yang bervariasi.');
        } else {
            $this->command->warn('Tidak ada Entry Meeting yang dibuat karena tidak ada milestone Entry Meeting.');
        }
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