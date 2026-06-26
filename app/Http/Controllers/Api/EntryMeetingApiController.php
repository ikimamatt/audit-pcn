<?php

namespace App\Http\Controllers\Api;

use App\Models\EntryMeeting;
use App\Http\Requests\Audit\PelaksanaanAudit\StoreEntryMeetingRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\UpdateEntryMeetingRequest;
use App\Services\Audit\EntryMeetingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EntryMeetingApiController extends BaseApiController
{
    public function __construct(
        protected EntryMeetingService $entryMeetingService
    ) {}

    /**
     * Daftar Entry Meeting (server-side paginated via Stored Procedure).
     * Query params: page, limit, search (nomor_surat_tugas), status
     */
    public function index(Request $request): JsonResponse
    {
        [$perPage, $page, $offset] = $this->resolvePagination($request);

        $search = $request->input('search') ?: null;
        $status = $request->input('status') ?: null;

        [$total, $rows] = $this->callSP('sp_get_entry_meeting', [
            $perPage, $offset, $search, $status,
        ]);

        return $this->successPaginated($rows, $total, $page, $perPage);
    }

    public function show(string $id): JsonResponse
    {
        $item = EntryMeeting::with(['auditee', 'programKerjaAudit.perencanaanAudit'])->find($id);
        if (! $item) {
            return $this->error('Entry Meeting tidak ditemukan.', 404);
        }
        return $this->success($item);
    }

    public function store(StoreEntryMeetingRequest $request): JsonResponse
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
            $item = $this->entryMeetingService->create($data);
            return $this->success($item, 'Entry Meeting berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateEntryMeetingRequest $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = EntryMeeting::find($id);
        if (! $item) {
            return $this->error('Entry Meeting tidak ditemukan.', 404);
        }

        $data = $request->validated();
        if ($request->hasFile('file_undangan')) {
            $data['file_undangan_file'] = $request->file('file_undangan');
        }
        if ($request->hasFile('file_absensi')) {
            $data['file_absensi_file'] = $request->file('file_absensi');
        }

        try {
            $this->entryMeetingService->update($item, $data);
            return $this->success($item->fresh(), 'Entry Meeting berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = EntryMeeting::find($id);
        if (! $item) {
            return $this->error('Entry Meeting tidak ditemukan.', 404);
        }

        try {
            $this->entryMeetingService->delete($item);
            return $this->success(null, 'Entry Meeting berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->error('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }

    public function approval(Request $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = EntryMeeting::find($id);
        if (! $item) {
            return $this->error('Entry Meeting tidak ditemukan.', 404);
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
