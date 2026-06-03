<?php

namespace App\Services\Audit;

use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\Models\Audit\PkaProsesBisnis;
use App\Models\Models\Audit\PkaRisiko;
use App\Models\Models\Audit\PkaKontrol;
use App\Models\Models\Audit\PkaMilestone;
use App\Models\Models\Audit\PkaDokumen;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ProgramKerjaAuditService
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Create a new Program Kerja Audit.
     *
     * @param array $data
     * @return ProgramKerjaAudit
     */
    public function create(array $data): ProgramKerjaAudit
    {
        return DB::transaction(function () use ($data) {
            // Collect process names for backward-compat JSON field
            $prosesBisnisNama = collect($data['proses_bisnis'] ?? [])
                ->pluck('nama')
                ->filter()
                ->values()
                ->toArray();

            // Save main PKA
            $pka = ProgramKerjaAudit::create([
                'perencanaan_audit_id' => $data['perencanaan_audit_id'],
                'tanggal_pka'          => $data['tanggal_pka'],
                'no_pka'               => $data['no_pka'],
                'judul_pka'            => $data['judul_pka'],
                'proses_bisnis'        => $prosesBisnisNama,
                'informasi_umum'       => $data['informasi_umum'] ?? null,
                'kpi_tidak_tercapai'   => $data['kpi_tidak_tercapai'] ?? null,
                'data_awal_dokumen'    => is_array($data['data_awal_dokumen'] ?? null)
                                            ? array_values($data['data_awal_dokumen'])
                                            : [],
            ]);

            // Save hierarchy (Proses Bisnis -> Risiko -> Kontrol)
            $this->storeHierarki($pka->id, $data['proses_bisnis'] ?? []);

            // Save milestones
            if (isset($data['milestone']) && is_array($data['milestone'])) {
                foreach ($data['milestone'] as $nama => $ms) {
                    PkaMilestone::create([
                        'program_kerja_audit_id' => $pka->id,
                        'nama_milestone'         => $nama,
                        'tanggal_mulai'          => $ms['mulai'] ?? null,
                        'tanggal_selesai'        => $ms['selesai'] ?? null,
                    ]);
                }
            }

            // Save documents
            if (isset($data['dokumen_files']) && is_array($data['dokumen_files'])) {
                foreach ($data['dokumen_files'] as $file) {
                    $path = $this->fileUploadService->store($file, 'dokumen_pka');
                    PkaDokumen::create([
                        'program_kerja_audit_id' => $pka->id,
                        'nama_dokumen'           => $file->getClientOriginalName(),
                        'file_path'              => $path,
                    ]);
                }
            }

            return $pka;
        });
    }

    /**
     * Update an existing Program Kerja Audit.
     *
     * @param ProgramKerjaAudit $pka
     * @param array $data
     * @return ProgramKerjaAudit
     */
    public function update(ProgramKerjaAudit $pka, array $data): ProgramKerjaAudit
    {
        return DB::transaction(function () use ($pka, $data) {
            // Collect process names for backward-compat JSON field
            $prosesBisnisNama = collect($data['proses_bisnis'] ?? [])
                ->pluck('nama')
                ->filter()
                ->values()
                ->toArray();

            $pka->update([
                'perencanaan_audit_id' => $data['perencanaan_audit_id'],
                'tanggal_pka'          => $data['tanggal_pka'],
                'no_pka'               => $data['no_pka'],
                'judul_pka'            => $data['judul_pka'],
                'proses_bisnis'        => $prosesBisnisNama,
                'informasi_umum'       => $data['informasi_umum'] ?? null,
                'kpi_tidak_tercapai'   => $data['kpi_tidak_tercapai'] ?? null,
                'data_awal_dokumen'    => is_array($data['data_awal_dokumen'] ?? null)
                                            ? array_values($data['data_awal_dokumen'])
                                            : [],
            ]);

            // Re-create hierarchy (cascade deletes processes, risks, controls automatically)
            $pka->prosesBisnis()->delete();
            $this->storeHierarki($pka->id, $data['proses_bisnis'] ?? []);

            // Re-create milestones
            $pka->milestones()->delete();
            if (isset($data['milestone']) && is_array($data['milestone'])) {
                foreach ($data['milestone'] as $nama => $ms) {
                    PkaMilestone::create([
                        'program_kerja_audit_id' => $pka->id,
                        'nama_milestone'         => $nama,
                        'tanggal_mulai'          => $ms['mulai'] ?? null,
                        'tanggal_selesai'        => $ms['selesai'] ?? null,
                    ]);
                }
            }

            // Save new documents
            if (isset($data['dokumen_files']) && is_array($data['dokumen_files'])) {
                foreach ($data['dokumen_files'] as $file) {
                    $path = $this->fileUploadService->store($file, 'dokumen_pka');
                    PkaDokumen::create([
                        'program_kerja_audit_id' => $pka->id,
                        'nama_dokumen'           => $file->getClientOriginalName(),
                        'file_path'              => $path,
                    ]);
                }
            }

            return $pka;
        });
    }

    /**
     * Delete Program Kerja Audit and all cascading resources.
     *
     * @param ProgramKerjaAudit $pka
     * @return void
     */
    public function delete(ProgramKerjaAudit $pka): void
    {
        DB::transaction(function () use ($pka) {
            // Delete entry meeting if any
            if ($pka->entryMeeting) {
                // Delete files first
                $this->fileUploadService->delete($pka->entryMeeting->file_undangan);
                $this->fileUploadService->delete($pka->entryMeeting->file_absensi);
                $pka->entryMeeting->delete();
            }

            // Delete walkthrough if any
            if ($pka->walkthroughAudit) {
                $this->fileUploadService->delete($pka->walkthroughAudit->file_bpm);
                $pka->walkthroughAudit->delete();
            }

            // Delete associated documents on disk
            foreach ($pka->dokumen as $dok) {
                $this->fileUploadService->delete($dok->file_path);
            }

            $pka->risks()->delete();
            $pka->milestones()->delete();
            $pka->dokumen()->delete();
            $pka->prosesBisnis()->delete(); // CASCADE -> risks -> controls

            $pka->delete();
        });
    }

    /**
     * Check relationship counts for PKA deletion warning.
     *
     * @param ProgramKerjaAudit $item
     * @return array
     */
    public function checkRelations(ProgramKerjaAudit $item): array
    {
        $relations = [];

        if ($item->entryMeeting) {
            $relations[] = '1 data Entry Meeting';
        }
        if ($item->walkthroughAudit) {
            $relations[] = '1 data Walkthrough Audit';
        }

        $pbCount = $item->prosesBisnis->count();
        if ($pbCount > 0) {
            $risikoCount = 0;
            $kontrolCount = 0;
            foreach ($item->prosesBisnis as $pb) {
                $risikoCount += $pb->risikoList->count();
                foreach ($pb->risikoList as $r) {
                    $kontrolCount += $r->kontrolList->count();
                }
            }
            $relations[] = "{$pbCount} Proses Bisnis ({$risikoCount} Risiko, {$kontrolCount} Kontrol)";
        }

        $milestoneCount = $item->milestones->count();
        if ($milestoneCount > 0) {
            $relations[] = "{$milestoneCount} Milestone";
        }
        $dokumenCount = $item->dokumen->count();
        if ($dokumenCount > 0) {
            $relations[] = "{$dokumenCount} Dokumen";
        }

        return $relations;
    }

    /**
     * Get hierarchy flat array of risks and controls for a planning audit.
     *
     * @param int $perencanaanId
     * @return array
     */
    public function getHierarkiFlat(int $perencanaanId): array
    {
        $pka = ProgramKerjaAudit::where('perencanaan_audit_id', $perencanaanId)
            ->with(['prosesBisnis.risikoList.kontrolList'])
            ->first();

        if (!$pka) {
            return [
                'has_hierarki' => false,
                'pka_id'       => null,
                'risiko'       => [],
            ];
        }

        $hasHierarki = $pka->prosesBisnis->isNotEmpty();

        if (!$hasHierarki) {
            return [
                'has_hierarki' => false,
                'pka_id'       => $pka->id,
                'risiko'       => [],
            ];
        }

        $risikoFlat = collect();
        foreach ($pka->prosesBisnis as $pb) {
            foreach ($pb->risikoList as $risiko) {
                $risikoFlat->push([
                    'id'               => $risiko->id,
                    'deskripsi_risiko' => $risiko->deskripsi_risiko,
                    'penyebab_risiko'  => $risiko->penyebab_risiko,
                    'dampak_risiko'    => $risiko->dampak_risiko,
                    'kontrol'          => $risiko->kontrolList->map(fn($k) => [
                        'id'                => $k->id,
                        'deskripsi_kontrol' => $k->deskripsi_kontrol,
                    ])->values(),
                ]);
            }
        }

        return [
            'has_hierarki' => true,
            'pka_id'       => $pka->id,
            'risiko'       => $risikoFlat->values(),
        ];
    }

    /**
     * Store hierarchy (Proses Bisnis -> Risiko -> Kontrol).
     *
     * @param int $pkaId
     * @param array $prosesBisnisList
     * @return void
     */
    private function storeHierarki(int $pkaId, array $prosesBisnisList): void
    {
        foreach ($prosesBisnisList as $pbUrutan => $pbData) {
            $namaPb = trim($pbData['nama'] ?? '');
            if ($namaPb === '') {
                continue;
            }

            $pb = PkaProsesBisnis::create([
                'program_kerja_audit_id' => $pkaId,
                'nama_proses_bisnis'     => $namaPb,
                'urutan'                 => $pbUrutan + 1,
            ]);

            foreach ($pbData['risiko'] ?? [] as $risikoUrutan => $risikoData) {
                $deskripsi = trim($risikoData['deskripsi_risiko'] ?? '');
                if ($deskripsi === '') {
                    continue;
                }

                $risiko = PkaRisiko::create([
                    'pka_proses_bisnis_id' => $pb->id,
                    'deskripsi_risiko'     => $deskripsi,
                    'level_risiko'         => $risikoData['level_risiko'] ?? null,
                    'penyebab_risiko'      => $risikoData['penyebab_risiko'] ?? null,
                    'dampak_risiko'        => $risikoData['dampak_risiko'] ?? null,
                    'urutan'               => $risikoUrutan + 1,
                ]);

                foreach ($risikoData['kontrol'] ?? [] as $kontrolUrutan => $kontrolData) {
                    $deskKontrol = trim($kontrolData['deskripsi_kontrol'] ?? '');
                    if ($deskKontrol === '') {
                        continue;
                    }

                    PkaKontrol::create([
                        'pka_risiko_id'     => $risiko->id,
                        'deskripsi_kontrol' => $deskKontrol,
                        'urutan'            => $kontrolUrutan + 1,
                    ]);
                }
            }
        }
    }
}
