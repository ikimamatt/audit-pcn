<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Audit\PerencanaanAudit;

class ProgramKerjaAuditSeeder extends Seeder
{
    /**
     * Hierarki risiko & kontrol sample untuk seeder.
     * Setiap PKA akan mendapatkan 3 proses bisnis, masing-masing dengan 2 risiko,
     * dan masing-masing risiko memiliki 2 kontrol.
     */
    private array $hierarkiTemplate = [
        [
            'nama_proses_bisnis' => 'Proses Perencanaan Kontrak',
            'risiko' => [
                [
                    'deskripsi_risiko' => 'Risiko ketidakpatuhan terhadap regulasi',
                    'penyebab_risiko'  => 'Perubahan regulasi yang tidak diikuti dengan baik oleh unit kerja',
                    'dampak_risiko'    => 'Sanksi dari regulator dan kerugian finansial perusahaan',
                    'kontrol'          => [
                        'Sistem monitoring regulasi dan update berkala kepada seluruh unit',
                        'Pelatihan kepatuhan regulasi secara berkala untuk staf terkait',
                    ],
                ],
                [
                    'deskripsi_risiko' => 'Risiko inefisiensi perencanaan anggaran',
                    'penyebab_risiko'  => 'Estimasi biaya tidak akurat dan kurangnya data historis',
                    'dampak_risiko'    => 'Pembengkakan anggaran dan gagal mencapai target keuangan',
                    'kontrol'          => [
                        'Review anggaran oleh komite keuangan sebelum disetujui',
                        'Benchmarking dengan data historis dan standar industri',
                    ],
                ],
            ],
        ],
        [
            'nama_proses_bisnis' => 'Proses Pelaksanaan Kontrak',
            'risiko' => [
                [
                    'deskripsi_risiko' => 'Risiko inefisiensi operasional',
                    'penyebab_risiko'  => 'Proses bisnis yang tidak optimal dan duplikasi pekerjaan',
                    'dampak_risiko'    => 'Peningkatan biaya operasional dan penurunan produktivitas',
                    'kontrol'          => [
                        'Review proses berkala dan implementasi best practices',
                        'Penerapan KPI operasional dengan monitoring mingguan',
                    ],
                ],
                [
                    'deskripsi_risiko' => 'Risiko kegagalan teknologi',
                    'penyebab_risiko'  => 'Sistem IT yang tidak handal dan kurangnya pemeliharaan',
                    'dampak_risiko'    => 'Gangguan layanan dan kehilangan data operasional',
                    'kontrol'          => [
                        'Backup sistem harian dan disaster recovery plan yang teruji',
                        'Maintenance preventif terjadwal dan monitoring sistem 24 jam',
                    ],
                ],
            ],
        ],
        [
            'nama_proses_bisnis' => 'Proses Penagihan Kontrak',
            'risiko' => [
                [
                    'deskripsi_risiko' => 'Risiko piutang tak tertagih',
                    'penyebab_risiko'  => 'Lemahnya proses verifikasi kredibilitas pelanggan',
                    'dampak_risiko'    => 'Kerugian finansial akibat piutang macet',
                    'kontrol'          => [
                        'Prosedur verifikasi kredit sebelum pemberian kontrak',
                        'Aging schedule piutang dengan eskalasi otomatis ke manajemen',
                    ],
                ],
                [
                    'deskripsi_risiko' => 'Risiko keterlambatan penagihan',
                    'penyebab_risiko'  => 'Proses invoicing yang lambat dan tidak terstruktur',
                    'dampak_risiko'    => 'Cash flow terganggu dan denda keterlambatan',
                    'kontrol'          => [
                        'Sistem invoicing otomatis dengan notifikasi jatuh tempo',
                        'SOP penagihan dengan batas waktu yang ketat dan terukur',
                    ],
                ],
            ],
        ],
    ];

    public function run(): void
    {
        $perencanaanAuditList = PerencanaanAudit::all();

        if ($perencanaanAuditList->isEmpty()) {
            $this->command->warn('Tidak ada data perencanaan audit. Skipping ProgramKerjaAuditSeeder.');
            return;
        }

        $dokumenData = [];

        foreach ($perencanaanAuditList as $index => $perencanaanAudit) {
            // ── 1. Insert PKA ──────────────────────────────────────────────
            $pkaId = DB::table('program_kerja_audit')->insertGetId([
                'perencanaan_audit_id' => $perencanaanAudit->id,
                'tanggal_pka'          => '2024-07-01',
                'no_pka'               => 'PKA-00' . ($index + 1) . '/2024',
                'judul_pka'            => 'Audit Kepatuhan dan Operasional ' . ($index + 1),
                // proses_bisnis JSON lama tidak diisi lagi (sudah digantikan hierarki)
                'informasi_umum'       => 'Program Kerja Audit untuk ' . $perencanaanAudit->jenis_audit
                                          . ' pada ' . ($perencanaanAudit->auditee->direktorat ?? 'Direktorat'),
                'kpi_tidak_tercapai'   => 'KPI yang tidak tercapai: Efisiensi operasional, Kepatuhan regulasi, Pengelolaan risiko',
                'data_awal_dokumen'    => json_encode([
                    ['nama_dokumen' => 'Laporan keuangan',  'ruang_lingkup' => 'Seluruh perusahaan',      'periode' => 'Q1 2024'],
                    ['nama_dokumen' => 'SOP Operasional',   'ruang_lingkup' => 'Departemen Operasional',  'periode' => 'Tahun 2024'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ── 2. Insert Hierarki Proses Bisnis → Risiko → Kontrol ───────
            foreach ($this->hierarkiTemplate as $pbUrutan => $pbData) {
                $pbId = DB::table('pka_proses_bisnis')->insertGetId([
                    'program_kerja_audit_id' => $pkaId,
                    'nama_proses_bisnis'     => $pbData['nama_proses_bisnis'],
                    'urutan'                 => $pbUrutan + 1,
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ]);

                foreach ($pbData['risiko'] as $risikoUrutan => $risikoData) {
                    $risikoId = DB::table('pka_risiko')->insertGetId([
                        'pka_proses_bisnis_id' => $pbId,
                        'deskripsi_risiko'     => $risikoData['deskripsi_risiko'],
                        'penyebab_risiko'      => $risikoData['penyebab_risiko'],
                        'dampak_risiko'        => $risikoData['dampak_risiko'],
                        'urutan'               => $risikoUrutan + 1,
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ]);

                    foreach ($risikoData['kontrol'] as $kontrolUrutan => $deskripsiKontrol) {
                        DB::table('pka_kontrol')->insert([
                            'pka_risiko_id'     => $risikoId,
                            'deskripsi_kontrol' => $deskripsiKontrol,
                            'urutan'            => $kontrolUrutan + 1,
                            'created_at'        => now(),
                            'updated_at'        => now(),
                        ]);
                    }
                }
            }

            // ── 3. Dokumen PKA ─────────────────────────────────────────────
            $dokumens = [
                ['nama_dokumen' => 'Program Kerja Audit ' . ($index + 1),     'file_path' => 'dokumen/pka_' . ($index + 1) . '.pdf',          'status_approval' => 'approved', 'approved_by' => 1, 'approved_at' => now()],
                ['nama_dokumen' => 'Lampiran Dokumen ' . ($index + 1),        'file_path' => 'dokumen/lampiran_' . ($index + 1) . '.pdf',     'status_approval' => 'pending',  'approved_by' => null, 'approved_at' => null],
                ['nama_dokumen' => 'Surat Tugas Audit ' . ($index + 1),       'file_path' => 'dokumen/surat_tugas_' . ($index + 1) . '.pdf',  'status_approval' => 'approved', 'approved_by' => 1, 'approved_at' => now()],
            ];

            foreach ($dokumens as $dok) {
                $dokumenData[] = array_merge($dok, [
                    'program_kerja_audit_id' => $pkaId,
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ]);
            }
        }

        if (!empty($dokumenData)) {
            DB::table('pka_dokumen')->insert($dokumenData);
        }

        $total = $perencanaanAuditList->count();
        $this->command->info("[ProgramKerjaAuditSeeder] Selesai.");
        $this->command->info("  → PKA          : {$total} dibuat");
        $this->command->info("  → Proses Bisnis: " . ($total * 3) . " dibuat");
        $this->command->info("  → Risiko       : " . ($total * 6) . " dibuat");
        $this->command->info("  → Kontrol      : " . ($total * 12) . " dibuat");
        $this->command->info("  → Dokumen      : " . count($dokumenData) . " dibuat");
    }
}