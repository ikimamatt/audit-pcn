<?php

namespace App\Http\Controllers\Api;

use App\Models\WalkthroughAudit;
use App\Http\Requests\Audit\PelaksanaanAudit\StoreWalkthroughRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\UpdateWalkthroughRequest;
use App\Services\Audit\WalkthroughService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalkthroughApiController extends BaseApiController
{
    public function __construct(
        protected WalkthroughService $walkthroughService
    ) {}

    /**
     * Daftar Walkthrough Audit (server-side paginated via Stored Procedure).
     * Query params: page, limit, search (nomor_surat_tugas), status
     */
    public function index(Request $request): JsonResponse
    {
        [$perPage, $page, $offset] = $this->resolvePagination($request);

        $search = $request->input('search') ?: null;
        $status = $request->input('status') ?: null;

        [$total, $rows] = $this->callSP('sp_get_walkthrough', [
            $perPage, $offset, $search, $status,
        ]);

        return $this->successPaginated($rows, $total, $page, $perPage);
    }

    public function show(string $id): JsonResponse
    {
        $item = WalkthroughAudit::with(['perencanaanAudit.auditee', 'programKerjaAudit'])->find($id);
        if (! $item) {
            return $this->error('Data walkthrough tidak ditemukan.', 404);
        }
        return $this->success($item);
    }

    public function store(StoreWalkthroughRequest $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $data = $request->validated();
        if ($request->hasFile('file_bpm')) {
            $data['file_bpm_file'] = $request->file('file_bpm');
        }

        try {
            $item = $this->walkthroughService->create($data);
            return $this->success($item, 'Walkthrough berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateWalkthroughRequest $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = WalkthroughAudit::find($id);
        if (! $item) {
            return $this->error('Data walkthrough tidak ditemukan.', 404);
        }

        $data = $request->validated();
        if ($request->hasFile('file_bpm')) {
            $data['file_bpm_file'] = $request->file('file_bpm');
        }

        try {
            $this->walkthroughService->update($item, $data);
            return $this->success($item->fresh(), 'Walkthrough berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, string $id): JsonResponse
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

    public function approval(Request $request, string $id): JsonResponse
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
