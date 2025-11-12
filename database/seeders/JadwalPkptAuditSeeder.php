<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MasterData\MasterAuditee;

class JadwalPkptAuditSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID dari auditee yang sudah ada
        $auditee = MasterAuditee::first();
        
        if (!$auditee) {
            $this->command->warn('Tidak ada data auditee. Skipping JadwalPkptAuditSeeder.');
            return;
        }

        DB::table('jadwal_pkpt_audits')->insert([
            [
                'auditee_id' => $auditee->id,
                'jenis_audit' => 'PKPT Tahunan',
                'jumlah_auditor' => 3,
                'tanggal_mulai' => '2024-07-01',
                'tanggal_selesai' => '2024-07-10',
                'status_approval' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'auditee_id' => $auditee->id,
                'jenis_audit' => 'PKPT Khusus',
                'jumlah_auditor' => 2,
                'tanggal_mulai' => '2024-08-01',
                'tanggal_selesai' => '2024-08-05',
                'status_approval' => 'approved',
                'approved_by' => 1,
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 