<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TodBpmAuditSeeder extends Seeder
{
    public function run(): void
    {
        $pkaList = \App\Models\Models\Audit\ProgramKerjaAudit::with([
            'perencanaanAudit',
            'prosesBisnis.risikoList.kontrolList',
        ])->get();

        if ($pkaList->isEmpty()) {
            $this->command->warn('[TodBpmAuditSeeder] Tidak ada data PKA. Seeder dilewati.');
            return;
        }

        // Ambil walkthrough approved yang punya file BPM
        $walkthroughApproved = DB::table('walkthrough_audit')
            ->where('status_approval', 'approved')
            ->whereNotNull('file_bpm')
            ->get()
            ->keyBy('perencanaan_audit_id');

        $statusOptions = ['pending', 'approved', 'rejected'];
        $statusWeights = [40, 40, 20];

        $todPivotRisiko  = [];
        $todPivotKontrol = [];
        $evaluasiData    = [];
        $todCount        = 0;

        foreach ($pkaList as $pka) {
            // Kumpulkan semua risiko (flat dari semua PB)
            $semuaRisiko = collect();
            foreach ($pka->prosesBisnis as $pb) {
                foreach ($pb->risikoList as $risiko) {
                    $semuaRisiko->push($risiko);
                }
            }

            if ($semuaRisiko->isEmpty()) {
                $this->command->warn("[TodBpmAuditSeeder] PKA #{$pka->id} tidak punya risiko hierarki. Dilewati.");
                continue;
            }

            // Tentukan file BPM dari walkthrough (jika ada)
            $walkthrough = $walkthroughApproved->get($pka->perencanaan_audit_id);
            $fileBpm     = $walkthrough ? $walkthrough->file_bpm : 'bpm/placeholder_' . $pka->id . '.pdf';
            $walkthroughId = $walkthrough ? $walkthrough->id : null;

            $randomStatus   = $this->getRandomStatus($statusOptions, $statusWeights);
            $rejectionReason = null;
            if ($randomStatus === 'rejected') {
                $alasan = [
                    'Dokumen BPM tidak lengkap, perlu dilengkapi sebelum dapat diapprove.',
                    'Judul BPM tidak sesuai dengan scope audit yang direncanakan.',
                    'File BPM yang diupload tidak dapat dibuka, perlu upload ulang.',
                ];
                $rejectionReason = $alasan[array_rand($alasan)];
            }

            $todId = DB::table('tod_bpm_audit')->insertGetId([
                'perencanaan_audit_id' => $pka->perencanaan_audit_id,
                'judul_bpm'            => 'Business Process Mapping - ' . ($pka->perencanaanAudit->jenis_audit ?? 'Audit'),
                'nama_bpo'             => 'BPO - ' . ($pka->perencanaanAudit->auditee->direktorat ?? 'Direktorat'),
                'file_bpm'             => $fileBpm,
                'resiko'               => null, // tidak digunakan, digantikan pivot
                'kontrol'              => null, // tidak digunakan, digantikan pivot
                'status_approval'      => $randomStatus,
                'rejection_reason'     => $rejectionReason,
                'approved_by'          => in_array($randomStatus, ['approved', 'rejected']) ? 1 : null,
                'approved_at'          => in_array($randomStatus, ['approved', 'rejected']) ? now() : null,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            $todCount++;

            // ── Pivot: pilih semua risiko dan kontrolnya ───────────────────
            foreach ($semuaRisiko as $risiko) {
                $todPivotRisiko[] = [
                    'tod_bpm_audit_id' => $todId,
                    'pka_risiko_id'    => $risiko->id,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];

                foreach ($risiko->kontrolList as $kontrol) {
                    $todPivotKontrol[] = [
                        'tod_bpm_audit_id' => $todId,
                        'pka_kontrol_id'   => $kontrol->id,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }
            }

            $evaluasiData[] = [
                'tod_bpm_audit_id' => $todId,
                'hasil_evaluasi'   => ['Cukup', 'Tidak Cukup'][array_rand(['Cukup', 'Tidak Cukup'])],
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }

        // Batch insert pivot dan evaluasi
        if (!empty($todPivotRisiko)) {
            DB::table('tod_bpm_risiko')->insert($todPivotRisiko);
        }
        if (!empty($todPivotKontrol)) {
            DB::table('tod_bpm_kontrol')->insert($todPivotKontrol);
        }
        if (!empty($evaluasiData)) {
            DB::table('tod_bpm_evaluasi')->insert($evaluasiData);
        }

        $this->command->info("[TodBpmAuditSeeder] Selesai.");
        $this->command->info("  → TOD dibuat      : {$todCount}");
        $this->command->info("  → Pivot Risiko    : " . count($todPivotRisiko));
        $this->command->info("  → Pivot Kontrol   : " . count($todPivotKontrol));
    }

    private function getRandomStatus(array $options, array $weights): string
    {
        $total   = array_sum($weights);
        $random  = mt_rand(1, $total);
        $current = 0;

        foreach ($options as $i => $opt) {
            $current += $weights[$i];
            if ($random <= $current) {
                return $opt;
            }
        }

        return $options[0];
    }
}