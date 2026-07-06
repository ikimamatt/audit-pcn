<?php

namespace App\Services\Audit;

use App\Models\ToeAudit;
use App\Models\ToeEvaluasi;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class ToeService
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Create a new TOE.
     *
     * @param array $data
     * @return ToeAudit
     */
    public function create(array $data): ToeAudit
    {
        return DB::transaction(function () use ($data) {
            $fileKkaToePath = null;
            if (isset($data['file_kka_toe_file'])) {
                $fileKkaToePath = $this->fileUploadService->store($data['file_kka_toe_file'], 'toe/kka-toe');
            }

            $toe = ToeAudit::create([
                'perencanaan_audit_id'   => $data['perencanaan_audit_id'],
                'judul_bpm'              => $data['judul_bpm'],
                'pengendalian_eksisting' => null,
                'pemilihan_sampel_audit' => $data['pemilihan_sampel_audit'] ?? null,
                'resiko'                 => null,
                'kontrol'                => null,
                'file_kka_toe'           => $fileKkaToePath,
            ]);

            // Save pivot risiko
            if (!empty($data['pka_risiko_ids'])) {
                foreach ($data['pka_risiko_ids'] as $risikoId) {
                    DB::table('toe_risiko')->insert([
                        'id'            => (string) \Illuminate\Support\Str::uuid(),
                        'toe_audit_id'  => $toe->id,
                        'pka_risiko_id' => $risikoId,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }

            // Save pivot kontrol
            if (!empty($data['pka_kontrol_ids'])) {
                foreach ($data['pka_kontrol_ids'] as $kontrolId) {
                    DB::table('toe_kontrol')->insert([
                        'id'             => (string) \Illuminate\Support\Str::uuid(),
                        'toe_audit_id'   => $toe->id,
                        'pka_kontrol_id' => $kontrolId,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            }

            ToeEvaluasi::create([
                'toe_audit_id'   => $toe->id,
                'hasil_evaluasi' => $data['hasil_evaluasi'] ?? null,
            ]);

            return $toe;
        });
    }

    /**
     * Update an existing TOE.
     *
     * @param ToeAudit $item
     * @param array $data
     * @return ToeAudit
     */
    public function update(ToeAudit $item, array $data): ToeAudit
    {
        return DB::transaction(function () use ($item, $data) {
            $updateData = [
                'perencanaan_audit_id'   => $data['perencanaan_audit_id'],
                'judul_bpm'              => $data['judul_bpm'],
                'pemilihan_sampel_audit' => $data['pemilihan_sampel_audit'] ?? null,
                'pengendalian_eksisting' => null,
                'resiko'                 => null,
                'kontrol'                => null,
            ];

            if (isset($data['file_kka_toe_file'])) {
                $updateData['file_kka_toe'] = $this->fileUploadService->replace(
                    $item->file_kka_toe,
                    $data['file_kka_toe_file'],
                    'toe/kka-toe'
                );
            }

            $item->update($updateData);

            // Sync pivot risiko
            DB::table('toe_risiko')->where('toe_audit_id', $item->id)->delete();
            if (!empty($data['pka_risiko_ids'])) {
                foreach ($data['pka_risiko_ids'] as $risikoId) {
                    DB::table('toe_risiko')->insert([
                        'id'            => (string) \Illuminate\Support\Str::uuid(),
                        'toe_audit_id'  => $item->id,
                        'pka_risiko_id' => $risikoId,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }

            // Sync pivot kontrol
            DB::table('toe_kontrol')->where('toe_audit_id', $item->id)->delete();
            if (!empty($data['pka_kontrol_ids'])) {
                foreach ($data['pka_kontrol_ids'] as $kontrolId) {
                    DB::table('toe_kontrol')->insert([
                        'id'             => (string) \Illuminate\Support\Str::uuid(),
                        'toe_audit_id'   => $item->id,
                        'pka_kontrol_id' => $kontrolId,
                        'created_at'     => now(),
                        'updated_at'     => now(),
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
     * Delete a TOE.
     *
     * @param ToeAudit $item
     * @return void
     */
    public function delete(ToeAudit $item): void
    {
        DB::transaction(function () use ($item) {
            if ($item->file_kka_toe) {
                $this->fileUploadService->delete($item->file_kka_toe);
            }
            $item->delete(); // cascade deletes pivot records automatically via DB FK constraint
        });
    }

    /**
     * Create a new TOE Evaluasi.
     *
     * @param array $data
     * @return ToeEvaluasi
     */
    public function createEvaluasi(array $data): ToeEvaluasi
    {
        return ToeEvaluasi::create([
            'toe_audit_id'   => $data['toe_audit_id'],
            'hasil_evaluasi' => $data['hasil_evaluasi'],
        ]);
    }

    /**
     * Update an existing TOE Evaluasi.
     *
     * @param ToeEvaluasi $item
     * @param array $data
     * @return ToeEvaluasi
     */
    public function updateEvaluasi(ToeEvaluasi $item, array $data): ToeEvaluasi
    {
        $item->update([
            'hasil_evaluasi' => $data['hasil_evaluasi'],
        ]);
        return $item;
    }

    /**
     * Delete a TOE Evaluasi.
     *
     * @param ToeEvaluasi $item
     * @return void
     */
    public function deleteEvaluasi(ToeEvaluasi $item): void
    {
        $item->delete();
    }
}
