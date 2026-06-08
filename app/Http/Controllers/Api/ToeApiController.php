<?php

namespace App\Http\Controllers\Api;

use App\Models\ToeAudit;
use App\Models\ToeEvaluasi;
use App\Services\Audit\ToeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ToeApiController extends BaseApiController
{
    public function __construct(
        protected ToeService $toeService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = ToeAudit::with(['perencanaanAudit.auditee', 'programKerjaAudit']);

        if ($request->filled('bulan')) {
            $query->whereHas('perencanaanAudit', function ($q) use ($request) {
                $bulan = \Carbon\Carbon::parse($request->bulan);
                $q->whereYear('tanggal_audit_mulai', $bulan->year)
                  ->whereMonth('tanggal_audit_mulai', $bulan->month);
            });
        }

        return $this->success($query->get());
    }

    public function show(int $id): JsonResponse
    {
        $item = ToeAudit::with(['perencanaanAudit.auditee', 'programKerjaAudit', 'evaluasi'])->find($id);
        if (! $item) {
            return $this->error('TOE tidak ditemukan.', 404);
        }
        return $this->success($item);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $validated = $request->validate([
            'program_kerja_audit_id' => 'required|exists:program_kerja_audit,id',
            'perencanaan_audit_id'   => 'required|exists:perencanaan_audit,id',
            'proses_bisnis'          => 'nullable|string',
            'risiko'                 => 'nullable|string',
            'kontrol'               => 'nullable|string',
            'toe'                    => 'nullable|string',
            'sumber_data'            => 'nullable|string',
        ]);

        try {
            $item = $this->toeService->create($validated);
            return $this->success($item, 'TOE berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = ToeAudit::find($id);
        if (! $item) {
            return $this->error('TOE tidak ditemukan.', 404);
        }

        $validated = $request->validate([
            'proses_bisnis' => 'nullable|string',
            'risiko'        => 'nullable|string',
            'kontrol'       => 'nullable|string',
            'toe'           => 'nullable|string',
            'sumber_data'   => 'nullable|string',
        ]);

        try {
            $this->toeService->update($item, $validated);
            return $this->success($item->fresh(), 'TOE berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = ToeAudit::find($id);
        if (! $item) {
            return $this->error('TOE tidak ditemukan.', 404);
        }

        try {
            $this->toeService->delete($item);
            return $this->success(null, 'TOE berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->error('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }

    public function approval(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = ToeAudit::find($id);
        if (! $item) {
            return $this->error('TOE tidak ditemukan.', 404);
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

    // ── Evaluasi ─────────────────────────────────────────────────

    public function evaluasiIndex(int $toeId): JsonResponse
    {
        $evaluasi = ToeEvaluasi::where('toe_audit_id', $toeId)->get();
        return $this->success($evaluasi);
    }

    public function evaluasiStore(Request $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $validated = $request->validate([
            'toe_audit_id'   => 'required|exists:toe_audit,id',
            'hasil_evaluasi' => 'nullable|string',
            'kesimpulan'     => 'nullable|string',
        ]);

        $item = ToeEvaluasi::create($validated);
        return $this->success($item, 'Evaluasi TOE berhasil disimpan.', 201);
    }

    public function evaluasiUpdate(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = ToeEvaluasi::find($id);
        if (! $item) {
            return $this->error('Evaluasi tidak ditemukan.', 404);
        }

        $item->update($request->only(['hasil_evaluasi', 'kesimpulan']));
        return $this->success($item->fresh(), 'Evaluasi TOE berhasil diupdate.');
    }

    public function evaluasiDestroy(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = ToeEvaluasi::find($id);
        if (! $item) {
            return $this->error('Evaluasi tidak ditemukan.', 404);
        }

        $item->delete();
        return $this->success(null, 'Evaluasi TOE berhasil dihapus.');
    }
}
