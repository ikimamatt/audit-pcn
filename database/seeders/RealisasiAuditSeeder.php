<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use App\Models\Audit\PerencanaanAudit;

class RealisasiAuditSeeder extends Seeder
{
    public function run(): void
    {
        // Get all perencanaan audit IDs
        $perencanaanAuditIds = PerencanaanAudit::pluck('id')->toArray();
        
        if (empty($perencanaanAuditIds)) {
            $this->command->warn('Tidak ada data perencanaan audit. Skipping RealisasiAuditSeeder.');
            return;
        }

        $realisasiData = [];
        
        foreach ($perencanaanAuditIds as $index => $perencanaanAuditId) {
            // Use only allowed enum values: 'selesai', 'on progress', 'belum'
            $statuses = ['selesai', 'on progress', 'belum'];
            $status = $statuses[$index % count($statuses)];
            
            // Status approval: 40% pending, 40% approved, 20% rejected
            $approvalStatuses = ['pending', 'approved', 'rejected'];
            $approvalWeights = [40, 40, 20];
            $approvalStatus = $this->getRandomStatus($approvalStatuses, $approvalWeights);
            
            // Generate rejection reason if status is rejected
            $rejectionReason = null;
            $approvedBy = null;
            $approvedAt = null;
            
            if ($approvalStatus === 'rejected') {
                $rejectionReasons = [
                    'Dokumen exit meeting tidak lengkap dan perlu dilengkapi terlebih dahulu.',
                    'Tanggal realisasi tidak sesuai dengan jadwal yang direncanakan.',
                    'File undangan dan absensi belum diupload sesuai ketentuan.',
                    'Status realisasi tidak sesuai dengan progress audit yang sebenarnya.',
                    'Dokumentasi exit meeting perlu perbaikan format dan konten.',
                ];
                $rejectionReason = $rejectionReasons[array_rand($rejectionReasons)];
                $approvedBy = 1; // User ID 1
                $approvedAt = now()->subDays(rand(1, 5));
            } elseif ($approvalStatus === 'approved') {
                $approvedBy = 1; // User ID 1
                $approvedAt = now()->subDays(rand(1, 10));
            }
            
            $realisasiData[] = [
                'perencanaan_audit_id' => $perencanaanAuditId,
                'tanggal_mulai' => '2024-07-' . (10 + $index),
                'tanggal_selesai' => '2024-07-' . (15 + $index),
                'status' => $status,
                'status_approval' => $approvalStatus,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
                'rejection_reason' => $rejectionReason,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('realisasi_audits')->insert($realisasiData);
        
        $this->command->info('RealisasiAudit seeder completed successfully!');
    }
    
    /**
     * Get random status based on weights
     */
    private function getRandomStatus($statuses, $weights)
    {
        $totalWeight = array_sum($weights);
        $random = mt_rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($statuses as $index => $status) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $status;
            }
        }
        
        return $statuses[0]; // Default to first status
    }
} 