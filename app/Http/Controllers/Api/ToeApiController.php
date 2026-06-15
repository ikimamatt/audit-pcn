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

    public function index(Request $request): JsonResponse
    {
        $query = ToeAudit::with(['perencanaanAudit.auditee', 'evaluasi', 'pkaRisiko', 'pkaKontrol']);

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

    public function update(UpdateToeRequest $request, int $id): JsonResponse
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

    public function evaluasiStore(StoreToeEvaluasiRequest $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = ToeEvaluasi::create($request->validated());
        return $this->success($item, 'Evaluasi TOE berhasil disimpan.', 201);
    }

    public function evaluasiUpdate(UpdateToeEvaluasiRequest $request, int $id): JsonResponse
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
