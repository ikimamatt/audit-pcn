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

    public function index(Request $request): JsonResponse
    {
        $query = EntryMeeting::with(['auditee', 'programKerjaAudit.perencanaanAudit']);

        if ($request->filled('bulan')) {
            $query->whereHas('programKerjaAudit.perencanaanAudit', function ($q) use ($request) {
                $bulan = \Carbon\Carbon::parse($request->bulan);
                $q->whereYear('tanggal_audit_mulai', $bulan->year)
                  ->whereMonth('tanggal_audit_mulai', $bulan->month);
            });
        }

        return $this->success($query->get());
    }

    public function show(int $id): JsonResponse
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

    public function update(UpdateEntryMeetingRequest $request, int $id): JsonResponse
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

    public function destroy(Request $request, int $id): JsonResponse
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

    public function approval(Request $request, int $id): JsonResponse
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
