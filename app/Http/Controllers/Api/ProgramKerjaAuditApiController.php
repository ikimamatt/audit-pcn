<?php

namespace App\Http\Controllers\Api;

use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\Models\Audit\PkaDokumen;
use App\Models\Audit\PerencanaanAudit;
use App\Services\Audit\ProgramKerjaAuditService;
use App\Services\Audit\PkaDocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramKerjaAuditApiController extends BaseApiController
{
    public function __construct(
        protected ProgramKerjaAuditService $pkaService,
        protected PkaDocumentService $documentService
    ) {}

    public function index(): JsonResponse
    {
        $data = ProgramKerjaAudit::with([
            'perencanaanAudit.auditee',
            'perencanaanAudit.area',
            'risks', 'milestones', 'dokumen',
        ])->get();

        return $this->success($data);
    }

    public function show(int $id): JsonResponse
    {
        $item = ProgramKerjaAudit::with([
            'perencanaanAudit.auditee',
            'perencanaanAudit.koordinator',
            'perencanaanAudit.ketuaTim',
            'prosesBisnis.risikoList.kontrolList',
            'risks', 'milestones', 'dokumen',
        ])->find($id);

        if (! $item) {
            return $this->error('Program Kerja Audit tidak ditemukan.', 404);
        }

        return $this->success($item);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $validated = $request->validate([
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'no_pka'               => 'required|string',
            'tujuan_audit'         => 'nullable|string',
            'sasaran_audit'        => 'nullable|string',
            'ruang_lingkup_audit'  => 'nullable|string',
            'metodologi_audit'     => 'nullable|string',
            'waktu_audit'          => 'nullable|string',
        ]);

        try {
            $pka = $this->pkaService->create($validated);
            return $this->success($pka, 'Program Kerja Audit berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $pka = ProgramKerjaAudit::find($id);
        if (! $pka) {
            return $this->error('Program Kerja Audit tidak ditemukan.', 404);
        }

        $validated = $request->validate([
            'perencanaan_audit_id' => 'sometimes|exists:perencanaan_audit,id',
            'no_pka'               => 'sometimes|string',
            'tujuan_audit'         => 'nullable|string',
            'sasaran_audit'        => 'nullable|string',
            'ruang_lingkup_audit'  => 'nullable|string',
            'metodologi_audit'     => 'nullable|string',
            'waktu_audit'          => 'nullable|string',
        ]);

        try {
            $this->pkaService->update($pka, $validated);
            return $this->success($pka->fresh(), 'Program Kerja Audit berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = ProgramKerjaAudit::find($id);
        if (! $item) {
            return $this->error('Program Kerja Audit tidak ditemukan.', 404);
        }

        try {
            $this->pkaService->delete($item);
            return $this->success(null, 'Program Kerja Audit berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->error('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Approval dokumen PKA.
     */
    public function approvalDokumen(Request $request, int $pkaId, int $dokId): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $dok = PkaDokumen::find($dokId);
        if (! $dok) {
            return $this->error('Dokumen tidak ditemukan.', 404);
        }

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $dok,
            $request->input('action'),
            $request->input('rejection_reason')
        );

        return $result['success']
            ? $this->success($dok->fresh(), $result['message'])
            : $this->error($result['message'], 403);
    }

    /**
     * Approval keseluruhan PKA.
     */
    public function approvalMain(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $pka = ProgramKerjaAudit::find($id);
        if (! $pka) {
            return $this->error('Program Kerja Audit tidak ditemukan.', 404);
        }

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $pka,
            $request->input('action'),
            $request->input('rejection_reason')
        );

        return $result['success']
            ? $this->success($pka->fresh(), $result['message'])
            : $this->error($result['message'], 403);
    }

    /**
     * Flat list Risiko + Kontrol dari PKA terkait surat tugas.
     */
    public function getHierarkiFlat(int $perencanaanId): JsonResponse
    {
        $data = $this->pkaService->getHierarkiFlat($perencanaanId);
        return $this->success($data);
    }

    /**
     * Cek relasi sebelum hapus.
     */
    public function checkRelations(int $id): JsonResponse
    {
        $item = ProgramKerjaAudit::with([
            'entryMeeting', 'walkthroughAudit',
            'prosesBisnis.risikoList.kontrolList',
            'milestones', 'dokumen',
        ])->find($id);

        if (! $item) {
            return $this->error('Program Kerja Audit tidak ditemukan.', 404);
        }

        $relations = $this->pkaService->checkRelations($item);

        return $this->success([
            'has_relations' => count($relations) > 0,
            'relations'     => $relations,
            'no_pka'        => $item->no_pka,
            'surat_tugas'   => $item->perencanaanAudit->nomor_surat_tugas ?? '-',
        ]);
    }
}
