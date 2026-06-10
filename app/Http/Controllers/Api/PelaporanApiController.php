<?php

namespace App\Http\Controllers\Api;

use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\Audit\PelaporanTemuan;
use App\Http\Requests\Audit\PelaporanAudit\StorePelaporanHasilAuditRequest;
use App\Http\Requests\Audit\PelaporanAudit\UpdatePelaporanHasilAuditRequest;
use App\Http\Requests\Audit\PelaporanAudit\UpdatePelaporanTemuanRequest;
use App\Services\Audit\PelaporanHasilAuditService;
use App\Services\Audit\NomorGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PelaporanApiController extends BaseApiController
{
    public function __construct(
        protected PelaporanHasilAuditService $pelaporanService,
        protected NomorGeneratorService $nomorService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $data = PelaporanHasilAudit::with([
            'perencanaanAudit.auditee',
            'perencanaanAudit.area',
            'temuan',
            'jenisAudit',
        ])->get();

        return $this->success($data);
    }

    public function show(int $id): JsonResponse
    {
        $item = PelaporanHasilAudit::with([
            'perencanaanAudit.auditee',
            'perencanaanAudit.area',
            'temuan.kodeAoi',
            'temuan.kodeRisk',
            'jenisAudit',
            'approver',
        ])->find($id);

        if (! $item) {
            return $this->error('Pelaporan Hasil Audit tidak ditemukan.', 404);
        }

        return $this->success($item);
    }

    public function store(StorePelaporanHasilAuditRequest $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        try {
            $item = $this->pelaporanService->create($request->validated());
            return $this->success($item, 'Pelaporan Hasil Audit berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdatePelaporanHasilAuditRequest $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = PelaporanHasilAudit::find($id);
        if (! $item) {
            return $this->error('Pelaporan Hasil Audit tidak ditemukan.', 404);
        }

        try {
            $this->pelaporanService->update($item, $request->validated());
            return $this->success($item->fresh(), 'Pelaporan Hasil Audit berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = PelaporanHasilAudit::find($id);
        if (! $item) {
            return $this->error('Pelaporan Hasil Audit tidak ditemukan.', 404);
        }

        try {
            $this->pelaporanService->delete($item);
            return $this->success(null, 'Pelaporan Hasil Audit berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->error('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }

    public function approval(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = PelaporanHasilAudit::find($id);
        if (! $item) {
            return $this->error('Pelaporan Hasil Audit tidak ditemukan.', 404);
        }

        $request->validate(['action' => 'required|in:approve,reject']);

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $item,
            $request->input('action'),
            $request->input('rejection_reason')
        );

        return $result['success']
            ? $this->success($item->fresh(), $result['message'])
            : $this->error($result['message'], 403);
    }

    /**
     * Daftar temuan untuk pelaporan tertentu.
     */
    public function getTemuan(int $id): JsonResponse
    {
        $temuan = PelaporanTemuan::with(['kodeAoi', 'kodeRisk'])
            ->where('pelaporan_hasil_audit_id', $id)
            ->get();

        return $this->success($temuan);
    }

    /**
     * Detail temuan.
     */
    public function getTemuanById(int $id): JsonResponse
    {
        $item = PelaporanTemuan::with(['kodeAoi', 'kodeRisk', 'pelaporanHasilAudit.perencanaanAudit'])->find($id);
        if (! $item) {
            return $this->error('Temuan tidak ditemukan.', 404);
        }
        return $this->success($item);
    }

    /**
     * Update temuan.
     */
    public function updateTemuan(UpdatePelaporanTemuanRequest $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = PelaporanTemuan::find($id);
        if (! $item) {
            return $this->error('Temuan tidak ditemukan.', 404);
        }

        $item->update($request->validated());
        return $this->success($item->fresh(), 'Temuan berhasil diupdate.');
    }

    /**
     * Semua temuan untuk penutup LHA.
     */
    public function getAllTemuanForPenutup(): JsonResponse
    {
        $data = PelaporanTemuan::with(['pelaporanHasilAudit.perencanaanAudit.auditee', 'kodeRisk'])
            ->whereHas('pelaporanHasilAudit', fn($q) => $q->where('status_approval', 'approved'))
            ->get();

        return $this->success($data);
    }

    /**
     * Generate nomor LHA/LHK.
     */
    public function generateNomorLhaLhk(Request $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $request->validate([
            'jenis_lha_lhk' => 'required|string',
            'jenis_audit_id' => 'required|integer|exists:master_jenis_audit,id',
            'kode_spi' => 'required|string',
        ]);

        $result = $this->nomorService->generateNomorLhaLhk(
            $request->input('jenis_lha_lhk'),
            (int) $request->input('jenis_audit_id'),
            $request->input('kode_spi')
        );

        return $this->success($result);
    }

    /**
     * Generate nomor ISS.
     */
    public function generateNomorIss(Request $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $request->validate([
            'kode_aoi_id'  => 'required|integer|exists:master_kode_aoi,id',
            'kode_risk_id' => 'required|integer|exists:master_kode_risk,id',
            'kode_spi'     => 'nullable|string',
        ]);

        $result = $this->nomorService->generateNomorIss(
            (int) $request->input('kode_aoi_id'),
            (int) $request->input('kode_risk_id'),
            $request->input('kode_spi', 'SPI.01.02')
        );

        return $this->success($result);
    }
}
