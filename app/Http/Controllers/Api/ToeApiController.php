<?php

namespace App\Http\Controllers\Api;

use App\Models\ToeAudit;
use App\Models\ToeEvaluasi;
use App\Http\Requests\Audit\PelaksanaanAudit\StoreToeRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\UpdateToeRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\StoreToeEvaluasiRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\UpdateToeEvaluasiRequest;
use App\Services\Audit\ToeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ToeApiController extends BaseApiController
{
    public function __construct(
        protected ToeService $toeService
    ) {}

    /**
     * Daftar TOE Audit (server-side paginated via Stored Procedure).
     * Query params: page, limit, search (nomor_surat_tugas), status
     */
    public function index(Request $request): JsonResponse
    {
        [$perPage, $page, $offset] = $this->resolvePagination($request);

        $search = $request->input('search') ?: null;
        $status = $request->input('status') ?: null;

        [$total, $rows] = $this->callSP('sp_get_toe', [
            $perPage, $offset, $search, $status,
        ]);

        $items = ToeAudit::hydrate($rows);
        $items->load(['perencanaanAudit.auditee', 'evaluasi', 'pkaRisiko', 'pkaKontrol']);

        return $this->successPaginated($items, $total, $page, $perPage);
    }

    public function show(string $id): JsonResponse
    {
        $item = ToeAudit::with(['perencanaanAudit.auditee', 'evaluasi', 'pkaRisiko', 'pkaKontrol'])->find($id);
        if (! $item) {
            return $this->error('TOE tidak ditemukan.', 404);
        }
        return $this->success($item);
    }

    public function store(StoreToeRequest $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $data = $request->validated();
        if ($request->hasFile('file_kka_toe')) {
            $data['file_kka_toe_file'] = $request->file('file_kka_toe');
        }

        try {
            $item = $this->toeService->create($data);
            return $this->success($item, 'TOE berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateToeRequest $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = ToeAudit::find($id);
        if (! $item) {
            return $this->error('TOE tidak ditemukan.', 404);
        }

        $data = $request->validated();
        if ($request->hasFile('file_kka_toe')) {
            $data['file_kka_toe_file'] = $request->file('file_kka_toe');
        }

        try {
            $this->toeService->update($item, $data);
            return $this->success($item->fresh(), 'TOE berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, string $id): JsonResponse
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

    public function approval(Request $request, string $id): JsonResponse
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

    public function evaluasiIndex(string $toeId): JsonResponse
    {
        $evaluasi = ToeEvaluasi::where('toe_audit_id', $toeId)->get();
        return $this->success($evaluasi);
    }

    public function evaluasiStore(StoreToeEvaluasiRequest $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = ToeEvaluasi::create($request->validated());
        return $this->success($item, 'Evaluasi TOE berhasil disimpan.', 201);
    }

    public function evaluasiUpdate(UpdateToeEvaluasiRequest $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = ToeEvaluasi::find($id);
        if (! $item) {
            return $this->error('Evaluasi tidak ditemukan.', 404);
        }

        $item->update($request->validated());
        return $this->success($item->fresh(), 'Evaluasi TOE berhasil diupdate.');
    }

    public function evaluasiDestroy(Request $request, string $id): JsonResponse
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
