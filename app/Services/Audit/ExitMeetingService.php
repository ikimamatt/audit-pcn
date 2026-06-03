<?php

namespace App\Services\Audit;

use App\Models\RealisasiAudit;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ExitMeetingService
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Create a new Exit Meeting.
     *
     * @param array $data
     * @return RealisasiAudit
     */
    public function create(array $data): RealisasiAudit
    {
        return DB::transaction(function () use ($data) {
            $insertData = [
                'perencanaan_audit_id' => $data['perencanaan_audit_id'],
                'tanggal_mulai'        => $data['tanggal_mulai'] ?? null,
                'tanggal_selesai'      => $data['tanggal_selesai'] ?? null,
            ];

            if (isset($data['file_undangan_file'])) {
                $insertData['file_undangan'] = $this->fileUploadService->store($data['file_undangan_file'], 'exit_meeting/undangan');
            }

            if (isset($data['file_absensi_file'])) {
                $insertData['file_absensi'] = $this->fileUploadService->store($data['file_absensi_file'], 'exit_meeting/absensi');
            }

            $realisasiAudit = RealisasiAudit::create($insertData);
            $this->updateStatusBasedOnDates($realisasiAudit);
            $realisasiAudit->save();

            return $realisasiAudit;
        });
    }

    /**
     * Update an existing Exit Meeting.
     *
     * @param RealisasiAudit $item
     * @param array $data
     * @return RealisasiAudit
     */
    public function update(RealisasiAudit $item, array $data): RealisasiAudit
    {
        return DB::transaction(function () use ($item, $data) {
            $updateData = [
                'perencanaan_audit_id' => $data['perencanaan_audit_id'],
                'tanggal_mulai'        => $data['tanggal_mulai'] ?? null,
                'tanggal_selesai'      => $data['tanggal_selesai'] ?? null,
            ];

            if (isset($data['file_undangan_file'])) {
                $updateData['file_undangan'] = $this->fileUploadService->replace(
                    $item->file_undangan,
                    $data['file_undangan_file'],
                    'exit_meeting/undangan'
                );
            }

            if (isset($data['file_absensi_file'])) {
                $updateData['file_absensi'] = $this->fileUploadService->replace(
                    $item->file_absensi,
                    $data['file_absensi_file'],
                    'exit_meeting/absensi'
                );
            }

            $item->update($updateData);
            $this->updateStatusBasedOnDates($item);
            $item->save();

            return $item;
        });
    }

    /**
     * Delete an Exit Meeting.
     *
     * @param RealisasiAudit $item
     * @return void
     */
    public function delete(RealisasiAudit $item): void
    {
        DB::transaction(function () use ($item) {
            if ($item->file_undangan) {
                $this->fileUploadService->delete($item->file_undangan);
            }
            if ($item->file_absensi) {
                $this->fileUploadService->delete($item->file_absensi);
            }
            $item->delete();
        });
    }

    /**
     * Update status berdasarkan tanggal secara otomatis
     *
     * @param RealisasiAudit $item
     * @return void
     */
    public function updateStatusBasedOnDates(RealisasiAudit $item): void
    {
        if ($item->tanggal_mulai && $item->tanggal_selesai) {
            $item->status = 'selesai';
        } elseif ($item->tanggal_mulai && !$item->tanggal_selesai) {
            $item->status = 'on progress';
        } else {
            $item->status = 'belum';
        }
    }

    /**
     * Process approval/rejection for Exit Meeting.
     *
     * @param RealisasiAudit $item
     * @param string $action
     * @param string|null $reason
     * @return array
     */
    public function approve(RealisasiAudit $item, string $action, ?string $reason): array
    {
        return DB::transaction(function () use ($item, $action, $reason) {
            $result = \App\Helpers\ApprovalHelper::processApproval(
                $item,
                $action,
                $reason
            );

            if ($result['success']) {
                $item->refresh();

                if ($action === 'approve' && $item->status_approval === 'approved') {
                    $item->status = 'selesai';
                    if (!$item->tanggal_selesai) {
                        $item->tanggal_selesai = now()->toDateString();
                    }
                    $item->save();
                } elseif ($action === 'reject') {
                    $this->updateStatusBasedOnDates($item);
                    $item->save();
                }
            }

            return $result;
        });
    }
}
