<?php

namespace App\Http\Controllers\Api;

use App\Models\RealisasiAudit;
use App\Services\Audit\ExitMeetingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExitMeetingApiController extends BaseApiController
{
    public function __construct(
        protected ExitMeetingService $exitMeetingService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = RealisasiAudit::with([
            'perencanaanAudit.auditee',
            'perencanaanAudit.programKerjaAudit.milestones',
        ]);

        if ($request->filled('bulan')) {
            $query->whereHas('perencanaanAudit', function ($q) use ($request) {
                $bulan = \Carbon\Carbon::parse($request->bulan);
                $q->whereYear('tanggal_audit_mulai', $bulan->year)
                  ->whereMonth('tanggal_audit_mulai', $bulan->month);
            });
        }

        return $this->success($query->orderByDesc('id')->get());
    }

    public function show(int $id): JsonResponse
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

    public function store(Request $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $validated = $request->validate([
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'tanggal_exit'         => 'required|date',
            'tempat'               => 'nullable|string',
            'agenda'               => 'nullable|string',
            'peserta'              => 'nullable|string',
            'catatan'              => 'nullable|string',
        ]);

        try {
            $item = $this->exitMeetingService->create($validated);
            return $this->success($item, 'Exit Meeting berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = RealisasiAudit::find($id);
        if (! $item) {
            return $this->error('Exit Meeting tidak ditemukan.', 404);
        }

        $validated = $request->validate([
            'tanggal_exit' => 'sometimes|date',
            'tempat'       => 'nullable|string',
            'agenda'       => 'nullable|string',
            'peserta'      => 'nullable|string',
            'catatan'      => 'nullable|string',
        ]);

        try {
            $this->exitMeetingService->update($item, $validated);
            return $this->success($item->fresh(), 'Exit Meeting berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
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

    public function approval(Request $request, int $id): JsonResponse
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
