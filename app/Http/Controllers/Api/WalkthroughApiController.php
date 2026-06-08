<?php

namespace App\Http\Controllers\Api;

use App\Models\WalkthroughAudit;
use App\Services\Audit\WalkthroughService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalkthroughApiController extends BaseApiController
{
    public function __construct(
        protected WalkthroughService $walkthroughService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = WalkthroughAudit::with(['perencanaanAudit.auditee', 'programKerjaAudit.perencanaanAudit']);

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
        $item = WalkthroughAudit::with(['perencanaanAudit.auditee', 'programKerjaAudit'])->find($id);
        if (! $item) {
            return $this->error('Data walkthrough tidak ditemukan.', 404);
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
            'tanggal_walkthrough'    => 'required|date',
            'proses_bisnis'          => 'nullable|string',
            'hasil_walkthrough'      => 'nullable|string',
            'catatan'                => 'nullable|string',
        ]);

        try {
            $item = $this->walkthroughService->create($validated);
            return $this->success($item, 'Walkthrough berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = WalkthroughAudit::find($id);
        if (! $item) {
            return $this->error('Data walkthrough tidak ditemukan.', 404);
        }

        $validated = $request->validate([
            'tanggal_walkthrough' => 'sometimes|date',
            'proses_bisnis'       => 'nullable|string',
            'hasil_walkthrough'   => 'nullable|string',
            'catatan'             => 'nullable|string',
        ]);

        try {
            $this->walkthroughService->update($item, $validated);
            return $this->success($item->fresh(), 'Walkthrough berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = WalkthroughAudit::find($id);
        if (! $item) {
            return $this->error('Data walkthrough tidak ditemukan.', 404);
        }

        try {
            $this->walkthroughService->delete($item);
            return $this->success(null, 'Walkthrough berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->error('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }

    public function approval(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = WalkthroughAudit::find($id);
        if (! $item) {
            return $this->error('Data walkthrough tidak ditemukan.', 404);
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
}
