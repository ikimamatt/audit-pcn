<?php

namespace App\Services\Audit;

use App\Models\TodBpmAudit;
use App\Models\TodBpmEvaluasi;
use App\Models\WalkthroughAudit;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class TodBpmService
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Create a new TOD BPM.
     *
     * @param array $data
     * @return TodBpmAudit
     */
    public function create(array $data): TodBpmAudit
    {
        return DB::transaction(function () use ($data) {
            $walkthrough = WalkthroughAudit::findOrFail($data['walkthrough_id']);
            if (!$walkthrough->file_bpm) {
                throw new \DomainException('File BPM dari walkthrough tidak ditemukan!');
            }

            $fileKkaTodPath = null;
            if (isset($data['file_kka_tod_file'])) {
                $fileKkaTodPath = $this->fileUploadService->store($data['file_kka_tod_file'], 'tod-bpm/kka-tod');
            }

            $bpm = TodBpmAudit::create([
                'perencanaan_audit_id' => $data['perencanaan_audit_id'],
                'judul_bpm'            => $data['judul_bpm'],
                'nama_bpo'             => $data['nama_bpo'],
                'file_bpm'             => $walkthrough->file_bpm,
                'file_kka_tod'         => $fileKkaTodPath,
                'resiko'               => null,
                'kontrol'              => null,
            ]);

            // Save pivot risiko
            if (!empty($data['pka_risiko_ids'])) {
                foreach ($data['pka_risiko_ids'] as $risikoId) {
                    DB::table('tod_bpm_risiko')->insert([
                        'id'               => (string) \Illuminate\Support\Str::uuid(),
                        'tod_bpm_audit_id' => $bpm->id,
                        'pka_risiko_id'    => $risikoId,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            }

            // Save pivot kontrol
            if (!empty($data['pka_kontrol_ids'])) {
                foreach ($data['pka_kontrol_ids'] as $kontrolId) {
                    DB::table('tod_bpm_kontrol')->insert([
                        'id'               => (string) \Illuminate\Support\Str::uuid(),
                        'tod_bpm_audit_id' => $bpm->id,
                        'pka_kontrol_id'   => $kontrolId,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            }

            TodBpmEvaluasi::create([
                'tod_bpm_audit_id' => $bpm->id,
                'hasil_evaluasi'   => $data['hasil_evaluasi'] ?? null,
            ]);

            return $bpm;
        });
    }

    /**
     * Update an existing TOD BPM.
     *
     * @param TodBpmAudit $item
     * @param array $data
     * @return TodBpmAudit
     */
    public function update(TodBpmAudit $item, array $data): TodBpmAudit
    {
        return DB::transaction(function () use ($item, $data) {
            $updateData = [
                'perencanaan_audit_id' => $data['perencanaan_audit_id'],
                'judul_bpm'            => $data['judul_bpm'],
                'nama_bpo'             => $data['nama_bpo'],
                'resiko'               => null,
                'kontrol'              => null,
            ];

            if (!empty($data['walkthrough_id'])) {
                $walkthrough = WalkthroughAudit::findOrFail($data['walkthrough_id']);
                if ($walkthrough->file_bpm) {
                    $updateData['file_bpm'] = $walkthrough->file_bpm;
                }
            }

            if (isset($data['file_kka_tod_file'])) {
                $updateData['file_kka_tod'] = $this->fileUploadService->replace(
                    $item->file_kka_tod,
                    $data['file_kka_tod_file'],
                    'tod-bpm/kka-tod'
                );
            }

            $item->update($updateData);

            // Sync pivot risiko
            DB::table('tod_bpm_risiko')->where('tod_bpm_audit_id', $item->id)->delete();
            if (!empty($data['pka_risiko_ids'])) {
                foreach ($data['pka_risiko_ids'] as $risikoId) {
                    DB::table('tod_bpm_risiko')->insert([
                        'id'               => (string) \Illuminate\Support\Str::uuid(),
                        'tod_bpm_audit_id' => $item->id,
                        'pka_risiko_id'    => $risikoId,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            }

            // Sync pivot kontrol
            DB::table('tod_bpm_kontrol')->where('tod_bpm_audit_id', $item->id)->delete();
            if (!empty($data['pka_kontrol_ids'])) {
                foreach ($data['pka_kontrol_ids'] as $kontrolId) {
                    DB::table('tod_bpm_kontrol')->insert([
                        'id'               => (string) \Illuminate\Support\Str::uuid(),
                        'tod_bpm_audit_id' => $item->id,
                        'pka_kontrol_id'   => $kontrolId,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            }

            // Sync/Update Hasil Evaluasi
            $firstEv = $item->evaluasi()->first();
            if ($firstEv) {
                $firstEv->update(['hasil_evaluasi' => $data['hasil_evaluasi'] ?? null]);
            } else {
                $item->evaluasi()->create(['hasil_evaluasi' => $data['hasil_evaluasi'] ?? null]);
            }

            return $item;
        });
    }

    /**
     * Delete a TOD BPM.
     *
     * @param TodBpmAudit $item
     * @return void
     */
    public function delete(TodBpmAudit $item): void
    {
        DB::transaction(function () use ($item) {
            if ($item->file_kka_tod) {
                $this->fileUploadService->delete($item->file_kka_tod);
            }
            $item->delete(); // cascade deletes pivot records automatically via DB FK constraint
        });
    }

    /**
     * Create a new TOD BPM Evaluasi.
     *
     * @param array $data
     * @return TodBpmEvaluasi
     */
    public function createEvaluasi(array $data): TodBpmEvaluasi
    {
        return TodBpmEvaluasi::create([
            'tod_bpm_audit_id' => $data['tod_bpm_audit_id'],
            'hasil_evaluasi'   => $data['hasil_evaluasi'],
        ]);
    }

    /**
     * Update an existing TOD BPM Evaluasi.
     *
     * @param TodBpmEvaluasi $item
     * @param array $data
     * @return TodBpmEvaluasi
     */
    public function updateEvaluasi(TodBpmEvaluasi $item, array $data): TodBpmEvaluasi
    {
        $item->update([
            'hasil_evaluasi' => $data['hasil_evaluasi'],
        ]);
        return $item;
    }

    /**
     * Delete a TOD BPM Evaluasi.
     *
     * @param TodBpmEvaluasi $item
     * @return void
     */
    public function deleteEvaluasi(TodBpmEvaluasi $item): void
    {
        $item->delete();
    }
}
