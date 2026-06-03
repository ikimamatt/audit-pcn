<?php

namespace App\Services\Audit;

use App\Models\WalkthroughAudit;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\MasterData\MasterAuditee;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class WalkthroughService
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Create a new Walkthrough Audit.
     *
     * @param array $data
     * @return WalkthroughAudit
     */
    public function create(array $data): WalkthroughAudit
    {
        return DB::transaction(function () use ($data) {
            $pka = ProgramKerjaAudit::with(['milestones' => function($query) {
                $query->where('nama_milestone', 'Walkthrough');
            }])->findOrFail($data['program_kerja_audit_id']);

            $plannedDate = $pka->milestones->first()->tanggal_mulai ?? ($data['planned_walkthrough_date'] ?? null);
            $auditee = MasterAuditee::findOrFail($data['auditee_id']);

            $fileBpmPath = null;
            if (isset($data['file_bpm_file'])) {
                $fileBpmPath = $this->fileUploadService->store($data['file_bpm_file'], 'walkthrough/bpm');
            }

            return WalkthroughAudit::create([
                'perencanaan_audit_id'     => $pka->perencanaan_audit_id,
                'program_kerja_audit_id'   => $data['program_kerja_audit_id'],
                'planned_walkthrough_date' => $plannedDate,
                'actual_walkthrough_date'  => $data['actual_walkthrough_date'] ?? null,
                'tanggal_walkthrough'      => $data['actual_walkthrough_date'] ?? $plannedDate,
                'auditee_nama'             => $auditee->divisi ?? $auditee->nama_bidang,
                'hasil_walkthrough'        => $data['hasil_walkthrough'] ?? null,
                'file_bpm'                 => $fileBpmPath,
            ]);
        });
    }

    /**
     * Update an existing Walkthrough Audit.
     *
     * @param WalkthroughAudit $item
     * @param array $data
     * @return WalkthroughAudit
     */
    public function update(WalkthroughAudit $item, array $data): WalkthroughAudit
    {
        return DB::transaction(function () use ($item, $data) {
            $pka = ProgramKerjaAudit::with(['milestones' => function($query) {
                $query->where('nama_milestone', 'Walkthrough');
            }])->findOrFail($data['program_kerja_audit_id']);

            $plannedDate = $pka->milestones->first()->tanggal_mulai ?? ($data['planned_walkthrough_date'] ?? null);
            $auditee = MasterAuditee::findOrFail($data['auditee_id']);

            $updateData = [
                'perencanaan_audit_id'     => $pka->perencanaan_audit_id,
                'program_kerja_audit_id'   => $data['program_kerja_audit_id'],
                'planned_walkthrough_date' => $plannedDate,
                'actual_walkthrough_date'  => $data['actual_walkthrough_date'] ?? null,
                'tanggal_walkthrough'      => $data['actual_walkthrough_date'] ?? $plannedDate,
                'auditee_nama'             => $auditee->divisi ?? $auditee->nama_bidang,
                'hasil_walkthrough'        => $data['hasil_walkthrough'] ?? null,
            ];

            if (isset($data['file_bpm_file'])) {
                $updateData['file_bpm'] = $this->fileUploadService->replace(
                    $item->file_bpm,
                    $data['file_bpm_file'],
                    'walkthrough/bpm'
                );
            }

            $item->update($updateData);
            return $item;
        });
    }

    /**
     * Delete a Walkthrough Audit.
     *
     * @param WalkthroughAudit $item
     * @return void
     */
    public function delete(WalkthroughAudit $item): void
    {
        DB::transaction(function () use ($item) {
            if ($item->file_bpm) {
                $this->fileUploadService->delete($item->file_bpm);
            }
            $item->delete();
        });
    }
}
