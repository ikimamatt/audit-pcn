<?php

namespace App\Services\Audit;

use App\Models\PenutupLhaRekomendasi;
use App\Models\PenutupLhaTindakLanjut;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class TindakLanjutService
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Store a new Tindak Lanjut.
     *
     * @param int $rekomendasiId
     * @param array $data
     * @return PenutupLhaTindakLanjut
     */
    public function storeTindakLanjut(string $rekomendasiId, array $data): PenutupLhaTindakLanjut
    {
        return DB::transaction(function () use ($rekomendasiId, $data) {
            $rekomendasi = PenutupLhaRekomendasi::findOrFail($rekomendasiId);

            // Filter out empty comments
            $validKomentar = array_filter($data['komentar'] ?? [], function($k) { 
                return trim($k) !== ''; 
            });
            
            // Combine all comments with separator
            $combinedKomentar = implode("\n\n---\n\n", $validKomentar);

            // Preserve current status or default to open
            $latestTindakLanjut = $rekomendasi->tindakLanjut()->orderBy('created_at', 'desc')->first();
            $statusTindakLanjut = $latestTindakLanjut ? $latestTindakLanjut->status_tindak_lanjut : ($rekomendasi->status_tindak_lanjut ?? 'open');

            $insertData = [
                'real_waktu'                 => $data['real_waktu'] ?? null,
                'komentar'                   => $combinedKomentar,
                'status_tindak_lanjut'       => $statusTindakLanjut,
                'penutup_lha_rekomendasi_id' => $rekomendasiId,
            ];

            if (isset($data['file_eviden_file'])) {
                $insertData['file_eviden'] = $this->fileUploadService->store($data['file_eviden_file'], 'eviden_tindak_lanjut');
            }

            $tindakLanjut = PenutupLhaTindakLanjut::create($insertData);

            // Reset approval status to pending and status tindak lanjut to on_progress
            $rekomendasi->update([
                'status_approval'      => 'pending',
                'status_tindak_lanjut' => 'on_progress'
            ]);

            return $tindakLanjut;
        });
    }

    /**
     * Update an existing Tindak Lanjut.
     *
     * @param int $id
     * @param array $data
     * @return PenutupLhaTindakLanjut
     */
    public function updateTindakLanjut(string $id, array $data): PenutupLhaTindakLanjut
    {
        return DB::transaction(function () use ($id, $data) {
            $tindakLanjut = PenutupLhaTindakLanjut::with(['rekomendasi'])->findOrFail($id);

            $updateData = [
                'real_waktu'           => $data['real_waktu'] ?? null,
                'komentar'             => $data['komentar'] ?? null,
                'status_tindak_lanjut' => $data['status_tindak_lanjut'] ?? 'open',
            ];

            if (isset($data['file_eviden_file'])) {
                $updateData['file_eviden'] = $this->fileUploadService->replace(
                    $tindakLanjut->file_eviden,
                    $data['file_eviden_file'],
                    'eviden_tindak_lanjut'
                );
            }

            $tindakLanjut->update($updateData);

            // Sync parent status if this is the latest follow-up
            $rekomendasi = $tindakLanjut->rekomendasi;
            $latestTindakLanjut = $rekomendasi->tindakLanjut()->orderBy('created_at', 'desc')->first();
            if ($latestTindakLanjut && $latestTindakLanjut->id == $tindakLanjut->id) {
                $rekomendasi->update([
                    'status_tindak_lanjut' => $data['status_tindak_lanjut']
                ]);
            }

            return $tindakLanjut;
        });
    }

    /**
     * Delete a Tindak Lanjut.
     *
     * @param int $id
     * @return int The recommendation ID for redirect
     */
    public function destroyTindakLanjut(string $id): string
    {
        return DB::transaction(function () use ($id) {
            $tindakLanjut = PenutupLhaTindakLanjut::findOrFail($id);
            $rekomendasiId = $tindakLanjut->penutup_lha_rekomendasi_id;

            if ($tindakLanjut->file_eviden) {
                $this->fileUploadService->delete($tindakLanjut->file_eviden);
            }

            $tindakLanjut->delete();
            return $rekomendasiId;
        });
    }
}
