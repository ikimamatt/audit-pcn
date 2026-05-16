<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToeAuditSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil TOD yang sudah approved (TOE bergantung pada TOD)
        $todList = DB::table('tod_bpm_audit')
            ->where('status_approval', 'approved')
            ->get();

        if ($todList->isEmpty()) {
            // Fallback: ambil semua TOD jika tidak ada yang approved
            $todList = DB::table('tod_bpm_audit')->get();
        }

        if ($todList->isEmpty()) {
            $this->command->warn('[ToeAuditSeeder] Tidak ada data TOD. Seeder dilewati.');
            return;
        }

        $statusOptions = ['pending', 'approved', 'rejected'];
        $statusWeights = [40, 40, 20];

        $toeData         = [];
        $toePivotRisiko  = [];
        $toePivotKontrol = [];
        $evaluasiData    = [];

        foreach ($todList as $tod) {
            // Ambil risiko & kontrol pivot dari TOD yang bersangkutan
            $todRisikoIds  = DB::table('tod_bpm_risiko')
                ->where('tod_bpm_audit_id', $tod->id)
                ->pluck('pka_risiko_id')
                ->toArray();

            $todKontrolIds = DB::table('tod_bpm_kontrol')
                ->where('tod_bpm_audit_id', $tod->id)
                ->pluck('pka_kontrol_id')
                ->toArray();

            if (empty($todRisikoIds)) {
                $this->command->warn("[ToeAuditSeeder] TOD #{$tod->id} tidak punya pivot risiko. Dilewati.");
                continue;
            }

            $randomStatus    = $this->getRandomStatus($statusOptions, $statusWeights);
            $rejectionReason = null;
            if ($randomStatus === 'rejected') {
                $alasan = [
                    'Dokumen TOE tidak lengkap, perlu dilengkapi sebelum diapprove.',
                    'Pengendalian yang diidentifikasi tidak sesuai dengan standar berlaku.',
                    'Evaluasi TOE menunjukkan hasil yang tidak memuaskan, perlu perbaikan.',
                ];
                $rejectionReason = $alasan[array_rand($alasan)];
            }

            // Insert TOE (tanpa pengendalian_eksisting — digantikan pivot kontrol)
            $toeId = DB::table('toe_audit')->insertGetId([
                'perencanaan_audit_id'    => $tod->perencanaan_audit_id,
                'judul_bpm'               => $tod->judul_bpm,
                'pemilihan_sampel_audit'  => 'Sampel audit dipilih berdasarkan risiko tinggi dan materialitas transaksi.',
                'resiko'                  => null, // digantikan pivot
                'kontrol'                 => null, // digantikan pivot
                // pengendalian_eksisting: NULL — akan dihapus di fase migrasi berikutnya
                'status_approval'         => $randomStatus,
                'rejection_reason'        => $rejectionReason,
                'approved_by'             => in_array($randomStatus, ['approved', 'rejected']) ? 1 : null,
                'approved_at'             => in_array($randomStatus, ['approved', 'rejected']) ? now() : null,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);

            // ── Pivot Risiko (sama dengan yang ada di TOD) ────────────────
            foreach ($todRisikoIds as $risikoId) {
                $toePivotRisiko[] = [
                    'toe_audit_id'  => $toeId,
                    'pka_risiko_id' => $risikoId,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }

            // ── Pivot Kontrol (sama dengan yang ada di TOD) ───────────────
            foreach ($todKontrolIds as $kontrolId) {
                $toePivotKontrol[] = [
                    'toe_audit_id'   => $toeId,
                    'pka_kontrol_id' => $kontrolId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            }

            // ── Evaluasi ──────────────────────────────────────────────────
            $evaluasiOptions = ['Efektif', 'Tidak Efektif', 'Efektif Sebagian'];
            $evaluasiData[] = [
                'toe_audit_id'   => $toeId,
                'hasil_evaluasi' => $evaluasiOptions[array_rand($evaluasiOptions)],
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        // Batch insert
        if (!empty($toePivotRisiko)) {
            DB::table('toe_risiko')->insert($toePivotRisiko);
        }
        if (!empty($toePivotKontrol)) {
            DB::table('toe_kontrol')->insert($toePivotKontrol);
        }
        if (!empty($evaluasiData)) {
            DB::table('toe_evaluasi')->insert($evaluasiData);
        }

        $this->command->info("[ToeAuditSeeder] Selesai.");
        $this->command->info("  → TOE dibuat      : " . count($evaluasiData));
        $this->command->info("  → Pivot Risiko    : " . count($toePivotRisiko));
        $this->command->info("  → Pivot Kontrol   : " . count($toePivotKontrol));
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