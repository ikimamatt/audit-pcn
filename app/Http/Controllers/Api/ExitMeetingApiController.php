<?php

namespace App\Http\Controllers\Api;

use App\Models\RealisasiAudit;
use App\Http\Requests\Audit\PelaksanaanAudit\StoreExitMeetingRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\UpdateExitMeetingRequest;
use App\Services\Audit\ExitMeetingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExitMeetingApiController extends BaseApiController
{
    public function __construct(
        protected ExitMeetingService $exitMeetingService
    ) {}

    /**
     * Daftar Exit Meeting (server-side paginated via Stored Procedure).
     * Query params: page, limit, search (nomor_surat_tugas), status
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->filled('id')) {
            $item = RealisasiAudit::with([
                'perencanaanAudit.auditee',
                'perencanaanAudit.programKerjaAudit.milestones'
            ])->find($request->id);
            $items = $item ? collect([$item]) : collect([]);
            return $this->successPaginated($items, $items->count(), 1, 15);
        }

        [$perPage, $page, $offset] = $this->resolvePagination($request);

        $search = $request->input('search') ?: null;
        $status = $request->input('status') ?: null;

        [$total, $rows] = $this->callSP('sp_get_exit_meeting', [
            $perPage, $offset, $search, $status,
        ]);

        $items = RealisasiAudit::hydrate($rows);
        $items->load([
            'perencanaanAudit.auditee',
            'perencanaanAudit.programKerjaAudit.milestones'
        ]);

        return $this->successPaginated($items, $total, $page, $perPage);
    }

    public function show(string $id): JsonResponse
    {
        $item = RealisasiAudit::with([
            'perencanaanAudit.auditee',
            'perencanaanAudit.programKerjaAudit.milestones',
        ])->find($id);

        if (! $item) {
            return $this->error('Exit Meeting tidak ditemukan.', 404);
        }
        return $this->success($item);
    }

    public function store(StoreExitMeetingRequest $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $data = $request->validated();
        if ($request->hasFile('file_undangan')) {
            $data['file_undangan_file'] = $request->file('file_undangan');
        }
        if ($request->hasFile('file_absensi')) {
            $data['file_absensi_file'] = $request->file('file_absensi');
        }

        try {
            $item = $this->exitMeetingService->create($data);
            return $this->success($item, 'Exit Meeting berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateExitMeetingRequest $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = RealisasiAudit::find($id);
        if (! $item) {
            return $this->error('Exit Meeting tidak ditemukan.', 404);
        }

        $data = $request->validated();
        if ($request->hasFile('file_undangan')) {
            $data['file_undangan_file'] = $request->file('file_undangan');
        }
        if ($request->hasFile('file_absensi')) {
            $data['file_absensi_file'] = $request->file('file_absensi');
        }

        try {
            $this->exitMeetingService->update($item, $data);
            return $this->success($item->fresh(), 'Exit Meeting berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = RealisasiAudit::find($id);
        if (! $item) {
            return $this->error('Exit Meeting tidak ditemukan.', 404);
        }

        try {
            $this->exitMeetingService->delete($item);
            return $this->success(null, 'Exit Meeting berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->error('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }

    public function approval(Request $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = RealisasiAudit::find($id);
        if (! $item) {
            return $this->error('Exit Meeting tidak ditemukan.', 404);
        }

        $request->validate(['action' => 'required|in:approve,reject']);

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $item,
            $request->input('action'),
            $request->input('rejection_reason')
        );

        if ($result['success']) {
            $item->refresh();

            // Jika approve final, update status jadi 'selesai'
            if ($request->input('action') == 'approve' && $item->status_approval === 'approved') {
                $item->status = 'selesai';
                if (! $item->tanggal_selesai) {
                    $item->tanggal_selesai = now();
                }
                $item->save();
            }

            // Jika reject, update status berdasarkan tanggal
            if ($request->input('action') == 'reject') {
                $this->exitMeetingService->updateStatusBasedOnDates($item);
                $item->save();
            }

            return $this->success($item->fresh(), $result['message']);
        }

        return $this->error($result['message'], 403);
    }
}
