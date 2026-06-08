<?php

namespace App\Http\Controllers\Api;

use App\Models\TodBpmAudit;
use App\Models\TodBpmEvaluasi;
use App\Services\Audit\TodBpmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodBpmApiController extends BaseApiController
{
    public function __construct(
        protected TodBpmService $todBpmService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = TodBpmAudit::with(['perencanaanAudit.auditee', 'programKerjaAudit']);

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
        $item = TodBpmAudit::with(['perencanaanAudit.auditee', 'programKerjaAudit', 'evaluasi'])->find($id);
        if (! $item) {
            return $this->error('TOD BPM tidak ditemukan.', 404);
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
            'tod'                    => 'nullable|string',
            'sumber_data'            => 'nullable|string',
        ]);

        try {
            $item = $this->todBpmService->create($validated);
            return $this->success($item, 'TOD BPM berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = TodBpmAudit::find($id);
        if (! $item) {
            return $this->error('TOD BPM tidak ditemukan.', 404);
        }

        $validated = $request->validate([
            'proses_bisnis' => 'nullable|string',
            'risiko'        => 'nullable|string',
            'kontrol'       => 'nullable|string',
            'tod'           => 'nullable|string',
            'sumber_data'   => 'nullable|string',
        ]);

        try {
            $this->todBpmService->update($item, $validated);
            return $this->success($item->fresh(), 'TOD BPM berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = TodBpmAudit::find($id);
        if (! $item) {
            return $this->error('TOD BPM tidak ditemukan.', 404);
        }

        try {
            $this->todBpmService->delete($item);
            return $this->success(null, 'TOD BPM berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->error('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }

    public function approval(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = TodBpmAudit::find($id);
        if (! $item) {
            return $this->error('TOD BPM tidak ditemukan.', 404);
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

    public function evaluasiIndex(int $bpmId): JsonResponse
    {
        $evaluasi = TodBpmEvaluasi::where('tod_bpm_audit_id', $bpmId)->get();
        return $this->success($evaluasi);
    }

    public function evaluasiStore(Request $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $validated = $request->validate([
            'tod_bpm_audit_id' => 'required|exists:tod_bpm_audit,id',
            'hasil_evaluasi'   => 'nullable|string',
            'kesimpulan'       => 'nullable|string',
        ]);

        $item = TodBpmEvaluasi::create($validated);
        return $this->success($item, 'Evaluasi TOD BPM berhasil disimpan.', 201);
    }

    public function evaluasiUpdate(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = TodBpmEvaluasi::find($id);
        if (! $item) {
            return $this->error('Evaluasi tidak ditemukan.', 404);
        }

        $item->update($request->only(['hasil_evaluasi', 'kesimpulan']));
        return $this->success($item->fresh(), 'Evaluasi TOD BPM berhasil diupdate.');
    }

    public function evaluasiDestroy(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = TodBpmEvaluasi::find($id);
        if (! $item) {
            return $this->error('Evaluasi tidak ditemukan.', 404);
        }

        $item->delete();
        return $this->success(null, 'Evaluasi TOD BPM berhasil dihapus.');
    }
}
