<?php

namespace App\Http\Controllers\Api;

use App\Models\TodBpmAudit;
use App\Models\TodBpmEvaluasi;
use App\Http\Requests\Audit\PelaksanaanAudit\StoreTodBpmRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\UpdateTodBpmRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\StoreTodBpmEvaluasiRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\UpdateTodBpmEvaluasiRequest;
use App\Services\Audit\TodBpmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodBpmApiController extends BaseApiController
{
    public function __construct(
        protected TodBpmService $todBpmService
    ) {}

    /**
     * Daftar TOD BPM (server-side paginated via Stored Procedure).
     * Query params: page, limit, search (nomor_surat_tugas), status
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->filled('id')) {
            $item = TodBpmAudit::with(['perencanaanAudit.auditee', 'evaluasi', 'pkaRisiko', 'pkaKontrol'])->find($request->id);
            $items = $item ? collect([$item]) : collect([]);
            return $this->successPaginated($items, $items->count(), 1, 15);
        }

        [$perPage, $page, $offset] = $this->resolvePagination($request);

        $search = $request->input('search') ?: null;
        $status = $request->input('status') ?: null;

        [$total, $rows] = $this->callSP('sp_get_tod_bpm', [
            $perPage, $offset, $search, $status,
        ]);

        $items = TodBpmAudit::hydrate($rows);
        $items->load(['perencanaanAudit.auditee', 'evaluasi', 'pkaRisiko', 'pkaKontrol']);

        return $this->successPaginated($items, $total, $page, $perPage);
    }

    public function show(string $id): JsonResponse
    {
        $item = TodBpmAudit::with(['perencanaanAudit.auditee', 'evaluasi', 'pkaRisiko', 'pkaKontrol'])->find($id);
        if (! $item) {
            return $this->error('TOD BPM tidak ditemukan.', 404);
        }
        return $this->success($item);
    }

    public function store(StoreTodBpmRequest $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $data = $request->validated();
        if ($request->hasFile('file_kka_tod')) {
            $data['file_kka_tod_file'] = $request->file('file_kka_tod');
        }

        try {
            $item = $this->todBpmService->create($data);
            return $this->success($item, 'TOD BPM berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateTodBpmRequest $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = TodBpmAudit::find($id);
        if (! $item) {
            return $this->error('TOD BPM tidak ditemukan.', 404);
        }

        $data = $request->validated();
        if ($request->hasFile('file_kka_tod')) {
            $data['file_kka_tod_file'] = $request->file('file_kka_tod');
        }

        try {
            $this->todBpmService->update($item, $data);
            return $this->success($item->fresh(), 'TOD BPM berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, string $id): JsonResponse
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

    public function approval(Request $request, string $id): JsonResponse
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

    public function evaluasiIndex(string $bpmId): JsonResponse
    {
        $evaluasi = TodBpmEvaluasi::where('tod_bpm_audit_id', $bpmId)->get();
        return $this->success($evaluasi);
    }

    public function evaluasiStore(StoreTodBpmEvaluasiRequest $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = TodBpmEvaluasi::create($request->validated());
        return $this->success($item, 'Evaluasi TOD BPM berhasil disimpan.', 201);
    }

    public function evaluasiUpdate(UpdateTodBpmEvaluasiRequest $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = TodBpmEvaluasi::find($id);
        if (! $item) {
            return $this->error('Evaluasi tidak ditemukan.', 404);
        }

        $item->update($request->validated());
        return $this->success($item->fresh(), 'Evaluasi TOD BPM berhasil diupdate.');
    }

    public function evaluasiDestroy(Request $request, string $id): JsonResponse
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
