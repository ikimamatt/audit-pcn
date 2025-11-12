<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Models\Audit\ProgramKerjaAudit;

class PkaMilestoneSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua ID dari program kerja audit yang sudah ada
        $programKerjaAuditList = ProgramKerjaAudit::all();
        
        if ($programKerjaAuditList->isEmpty()) {
            $this->command->warn('Tidak ada data program kerja audit. Skipping PkaMilestoneSeeder.');
            return;
        }

        $milestoneData = [];
        
        foreach ($programKerjaAuditList as $index => $pka) {
            // Create 6 separate milestones for each PKA with sequential date ranges
            // Mengikuti aturan: tidak boleh overlap, berurutan, dan tidak boleh sama hari
            $baseDate = date('Y-m-d', strtotime('2024-07-01 + ' . ($index * 30) . ' days'));
            
            $milestones = [
                [
                    'nama_milestone' => 'Entry Meeting',
                    'tanggal_mulai' => $baseDate,
                    'tanggal_selesai' => date('Y-m-d', strtotime($baseDate . ' + 4 days')),
                ],
                [
                    'nama_milestone' => 'Walkthrough',
                    'tanggal_mulai' => date('Y-m-d', strtotime($baseDate . ' + 5 days')),
                    'tanggal_selesai' => date('Y-m-d', strtotime($baseDate . ' + 19 days')),
                ],
                [
                    'nama_milestone' => 'TOD',
                    'tanggal_mulai' => date('Y-m-d', strtotime($baseDate . ' + 20 days')),
                    'tanggal_selesai' => date('Y-m-d', strtotime($baseDate . ' + 39 days')),
                ],
                [
                    'nama_milestone' => 'TOE',
                    'tanggal_mulai' => date('Y-m-d', strtotime($baseDate . ' + 40 days')),
                    'tanggal_selesai' => date('Y-m-d', strtotime($baseDate . ' + 54 days')),
                ],
                [
                    'nama_milestone' => 'Draf LHA',
                    'tanggal_mulai' => date('Y-m-d', strtotime($baseDate . ' + 55 days')),
                    'tanggal_selesai' => date('Y-m-d', strtotime($baseDate . ' + 69 days')),
                ],
                [
                    'nama_milestone' => 'Exit Meeting',
                    'tanggal_mulai' => date('Y-m-d', strtotime($baseDate . ' + 70 days')),
                    'tanggal_selesai' => date('Y-m-d', strtotime($baseDate . ' + 74 days')),
                ],
            ];

            foreach ($milestones as $milestone) {
                $milestoneData[] = [
                    'program_kerja_audit_id' => $pka->id,
                    'nama_milestone' => $milestone['nama_milestone'],
                    'tanggal_mulai' => $milestone['tanggal_mulai'],
                    'tanggal_selesai' => $milestone['tanggal_selesai'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert all milestone data
        if (!empty($milestoneData)) {
            DB::table('pka_milestone')->insert($milestoneData);
            $this->command->info('PKA Milestone seeder berhasil dijalankan.');
        }
    }
} 