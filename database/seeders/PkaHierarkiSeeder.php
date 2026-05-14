<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PkaHierarkiSeeder extends Seeder
{
    /**
     * Migrasi data lama dari pka_risk_based_audit + proses_bisnis JSON
     * ke struktur hierarki baru: pka_proses_bisnis → pka_risiko → pka_kontrol
     *
     * Data lama di pka_risk_based_audit TIDAK dihapus (tetap sebagai arsip).
     * Seeder ini idempotent: skip PKA yang sudah punya data di pka_proses_bisnis.
     */
    public function run(): void
    {
        $pkaList = DB::table('program_kerja_audit')->get();

        if ($pkaList->isEmpty()) {
            $this->command->warn('[PkaHierarkiSeeder] Tidak ada data program_kerja_audit. Seeder dilewati.');
            return;
        }

        $pbInserted   = 0;
        $riskInserted = 0;
        $kontrolInserted = 0;
        $skipped      = 0;

        foreach ($pkaList as $pka) {
            // Idempotent: skip jika PKA ini sudah punya data di tabel baru
            $alreadyExists = DB::table('pka_proses_bisnis')
                ->where('program_kerja_audit_id', $pka->id)
                ->exists();

            if ($alreadyExists) {
                $skipped++;
                continue;
            }

            // ── 1. Parse proses_bisnis JSON lama ──────────────────────────
            $prosesBisnisRaw = json_decode($pka->proses_bisnis ?? '[]', true) ?? [];

            // Pastikan minimal ada 1 proses bisnis (fallback jika JSON kosong)
            if (empty($prosesBisnisRaw)) {
                $prosesBisnisRaw = ['(Proses Bisnis Tidak Tercatat)'];
            }

            // ── 2. Ambil semua risiko lama untuk PKA ini ──────────────────
            $risikoLama = DB::table('pka_risk_based_audit')
                ->where('program_kerja_audit_id', $pka->id)
                ->orderBy('id')
                ->get();

            // ── 3. Insert pka_proses_bisnis ───────────────────────────────
            foreach ($prosesBisnisRaw as $urutanPb => $namaPb) {
                $pbId = DB::table('pka_proses_bisnis')->insertGetId([
                    'program_kerja_audit_id' => $pka->id,
                    'nama_proses_bisnis'     => $namaPb,
                    'urutan'                 => $urutanPb + 1,
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ]);
                $pbInserted++;

                // ── 4. Distribusikan risiko lama ke proses bisnis ─────────
                // Strategi: distribusikan risiko lama secara merata ke semua PB.
                // Risiko yang "jatuh" ke PB ini ditentukan berdasarkan index modulo.
                $totalPb = count($prosesBisnisRaw);

                foreach ($risikoLama as $urutanRisiko => $risiko) {
                    // Hanya masukkan risiko yang "milik" PB ini (distribusi merata)
                    if ($urutanRisiko % $totalPb !== $urutanPb) {
                        continue;
                    }

                    // ── 5. Insert pka_risiko ──────────────────────────────
                    $risikoId = DB::table('pka_risiko')->insertGetId([
                        'pka_proses_bisnis_id' => $pbId,
                        'deskripsi_risiko'     => $risiko->deskripsi_resiko,
                        'penyebab_risiko'      => $risiko->penyebab_resiko ?? null,
                        'dampak_risiko'        => $risiko->dampak_resiko ?? null,
                        'urutan'               => (int) floor($urutanRisiko / $totalPb) + 1,
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ]);
                    $riskInserted++;

                    // ── 6. Insert pka_kontrol (dari pengendalian_eksisting) ──
                    $pengendalian = trim($risiko->pengendalian_eksisting ?? '');
                    if ($pengendalian !== '') {
                        DB::table('pka_kontrol')->insert([
                            'pka_risiko_id'      => $risikoId,
                            'deskripsi_kontrol'  => $pengendalian,
                            'urutan'             => 1,
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ]);
                        $kontrolInserted++;
                    }
                }
            }
        }

        $this->command->info("[PkaHierarkiSeeder] Selesai.");
        $this->command->info("  → Proses Bisnis   : {$pbInserted} baris dibuat");
        $this->command->info("  → Risiko          : {$riskInserted} baris dibuat");
        $this->command->info("  → Kontrol         : {$kontrolInserted} baris dibuat");
        if ($skipped > 0) {
            $this->command->warn("  → Dilewati (sudah ada): {$skipped} PKA");
        }
    }
}
