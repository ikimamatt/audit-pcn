<?php

namespace App\Http\Controllers\Api;

use App\Models\EntryMeeting;
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

    public function store(Request $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $validated = $request->validate([
            'program_kerja_audit_id' => 'required|exists:program_kerja_audit,id',
            'tanggal_entry'          => 'required|date',
            'tempat'                 => 'nullable|string',
            'agenda'                 => 'nullable|string',
            'peserta'                => 'nullable|string',
            'catatan'                => 'nullable|string',
        ]);

        try {
            $item = $this->entryMeetingService->create($validated);
            return $this->success($item, 'Entry Meeting berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = EntryMeeting::find($id);
        if (! $item) {
            return $this->error('Entry Meeting tidak ditemukan.', 404);
        }

        $validated = $request->validate([
            'tanggal_entry' => 'sometimes|date',
            'tempat'        => 'nullable|string',
            'agenda'        => 'nullable|string',
            'peserta'       => 'nullable|string',
            'catatan'       => 'nullable|string',
        ]);

        try {
            $this->entryMeetingService->update($item, $validated);
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
