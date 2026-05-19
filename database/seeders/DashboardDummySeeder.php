<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Audit\PerencanaanAudit;
use App\Models\RealisasiAudit;
use App\Models\Audit\PelaporanHasilAudit;
use App\Models\Audit\PelaporanTemuan;
use App\Models\PenutupLhaRekomendasi;

class DashboardDummySeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks just in case we hit missing references for optional fields
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing related dummy data if we want a fresh dashboard view? 
        // No, let's just insert new ones so we don't break existing stuff. We can insert ~30 audits.
        
        $auditeeIds = DB::table('master_auditee')->pluck('id')->toArray();
        $riskIds = DB::table('master_kode_risk')->pluck('id')->toArray();
        
        if (empty($auditeeIds) || empty($riskIds)) {
            $this->command->error('Tabel Master Auditee atau Kode Risk masih kosong!');
            return;
        }

        $now = Carbon::now();
        $statuses = ['selesai', 'on progress', 'belum'];
        $tlStatuses = ['closed', 'on_progress', 'open'];
        
        for ($i = 0; $i < 30; $i++) {
            $auditeeId = $auditeeIds[array_rand($auditeeIds)];
            
            // Perencanaan Audit
            $startPlan = $now->copy()->subDays(rand(10, 100));
            $endPlan = $startPlan->copy()->addDays(rand(7, 21));
            
            $pId = DB::table('perencanaan_audit')->insertGetId([
                'auditee_id' => $auditeeId,
                'jenis_audit_id' => 1,
                'jenis_audit' => 'RBA',
                'unit_id' => 1,
                'koordinator_id' => 1,
                'ketua_tim_id' => 1,
                'auditor' => json_encode([1]),
                'ruang_lingkup' => json_encode(['Finance']),
                'periode_audit' => $now->year,
                'nomor_surat_tugas' => 'ST/DUMMY/' . $now->year . '/' . rand(1000, 9999),
                'tanggal_surat_tugas' => $startPlan->copy()->subDays(2)->toDateString(),
                'tanggal_audit_mulai' => $startPlan->toDateString(),
                'tanggal_audit_sampai' => $endPlan->toDateString(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Program Kerja Audit & Milestones
            $pkaId = DB::table('program_kerja_audit')->insertGetId([
                'perencanaan_audit_id' => $pId,
                'tanggal_pka' => $startPlan->copy()->subDays(1)->toDateString(),
                'no_pka' => 'PKA/DUMMY/' . $now->year . '/' . rand(1000, 9999),
                'status_approval' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('pka_milestone')->insert([
                [
                    'program_kerja_audit_id' => $pkaId,
                    'nama_milestone' => 'Entry Meeting',
                    'tanggal_mulai' => $startPlan->toDateString(),
                    'tanggal_selesai' => $startPlan->copy()->addDays(2)->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'program_kerja_audit_id' => $pkaId,
                    'nama_milestone' => 'Exit Meeting',
                    'tanggal_mulai' => $endPlan->copy()->subDays(2)->toDateString(),
                    'tanggal_selesai' => $endPlan->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);

            // Realisasi Audit
            $statusRealisasi = $statuses[array_rand($statuses)];
            $startActual = null;
            $endActual = null;
            
            if ($statusRealisasi !== 'belum') {
                $startActual = $startPlan->copy()->addDays(rand(-2, 5)); // starts around the same time
                if ($statusRealisasi === 'selesai') {
                    // Usually takes 10-30 days
                    $endActual = $startActual->copy()->addDays(rand(10, 30));
                }

                // Entry Meeting (Tandanya Audit Dimulai)
                DB::table('entry_meeting')->insert([
                    'tanggal' => $startPlan->toDateString(),
                    'actual_meeting_date' => $startActual->toDateString(),
                    'auditee_id' => $auditeeId,
                    'program_kerja_audit_id' => $pkaId,
                    'file_undangan' => 'dummy_undangan.pdf',
                    'file_absensi' => 'dummy_absensi.pdf',
                    'status_approval' => 'approved',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Realisasi Audit (Exit Meeting)
            DB::table('realisasi_audits')->insert([
                'perencanaan_audit_id' => $pId,
                // Di menu Exit Meeting, tanggal_mulai/selesai adalah waktu rapat Exit, bukan waktu mulai audit
                'tanggal_mulai' => $endActual ? $endActual->copy()->subDays(2)->toDateString() : null, 
                'tanggal_selesai' => $endActual ? $endActual->toDateString() : null,
                'status' => $statusRealisasi,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Only create temuan if it is selesai or on progress
            if ($statusRealisasi !== 'belum') {
                $phaId = DB::table('pelaporan_hasil_audit')->insertGetId([
                    'perencanaan_audit_id' => $pId,
                    'nomor_lha_lhk' => 'LHK/DUMMY/' . rand(1000, 9999),
                    'jenis_lha_lhk' => 'LHK',
                    'kode_spi' => 'SPI',
                    'jenis_audit_id' => 1,
                    'nomor_urut' => $i + 1,
                    'tahun' => $now->year,
                    'status_approval' => 'approved',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Create 1-5 temuan per audit
                $numTemuan = rand(1, 6);
                for ($j = 0; $j < $numTemuan; $j++) {
                    $riskId = $riskIds[array_rand($riskIds)];
                    
                    $temuanId = DB::table('pelaporan_temuan')->insertGetId([
                        'pelaporan_hasil_audit_id' => $phaId,
                        'kode_risk_id' => $riskId,
                        'kode_aoi_id' => 1,
                        'hasil_temuan' => 'Dummy Temuan ' . $j . ' untuk Audit ' . $pId,
                        'nomor_iss' => 'ISS/DUMMY/' . rand(1000, 9999),
                        'nomor_urut_iss' => $j + 1,
                        'tahun' => $now->year,
                        'permasalahan' => 'Dummy Permasalahan',
                        'penyebab' => 'Dummy Penyebab',
                        'kriteria' => 'Dummy Kriteria',
                        'dampak_terjadi' => 'Dummy Dampak Terjadi',
                        'dampak_potensi' => 'Dummy Dampak Potensi',
                        'signifikan' => '1',
                        'status_approval' => 'approved',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Create tindak lanjut
                    $tlStatus = $tlStatuses[array_rand($tlStatuses)];
                    
                    // Simulasi target_waktu (ada yang di masa depan, ada yang kadaluarsa/overdue)
                    // Offset positif = masa lalu (terlambat)
                    // Offset negatif = masa depan (masih sesuai target)
                    $randomOffsets = [
                        rand(-40, -10), // Masa depan (Sesuai Target)
                        rand(-40, -10), // Masa depan (Sesuai Target)
                        rand(1, 29),    // Terlambat < 30 Hari
                        rand(31, 59),   // Terlambat 31-60 Hari
                        rand(61, 89),   // Terlambat 61-90 Hari
                        rand(91, 150)   // Terlambat > 90 Hari
                    ];
                    $offset = $randomOffsets[array_rand($randomOffsets)];
                    
                    $targetWaktu = $now->copy()->subDays($offset);
                    $realWaktu = ($tlStatus !== 'open') ? $targetWaktu->copy()->addDays(rand(-5, 10)) : null;
                    
                    $rekomId = DB::table('penutup_lha_rekomendasi')->insertGetId([
                        'pelaporan_isi_lha_id' => $temuanId,
                        'rekomendasi' => 'Dummy Rekomendasi ' . $j,
                        'rencana_aksi' => 'Dummy Rencana Aksi',
                        'eviden_rekomendasi' => 'dummy.pdf',
                        'pic_rekomendasi' => '["Finance"]',
                        'target_waktu' => $targetWaktu->toDateString(),
                        'real_waktu' => $realWaktu ? $realWaktu->toDateString() : null,
                        'status_tindak_lanjut' => $tlStatus,
                        'status_approval' => 'approved',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    if ($tlStatus !== 'open') {
                        DB::table('penutup_lha_tindak_lanjut')->insert([
                            'penutup_lha_rekomendasi_id' => $rekomId,
                            'real_waktu' => $realWaktu->toDateString(),
                            'komentar' => 'Dummy bukti pengerjaan tindak lanjut...',
                            'file_eviden' => 'dummy_bukti.pdf',
                            'status_tindak_lanjut' => $tlStatus,
                            'created_at' => $realWaktu->toDateTimeString(),
                            'updated_at' => $realWaktu->toDateTimeString()
                        ]);
                    }
                }
            }
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Berhasil membuat 30 dummy data audit komprehensif!');
    }
}
