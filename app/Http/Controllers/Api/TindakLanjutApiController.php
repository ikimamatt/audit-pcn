<?php

namespace App\Http\Controllers\Api;

use App\Models\PenutupLhaRekomendasi;
use App\Http\Requests\Audit\TindakLanjut\UpdatePemantauanRekomendasiRequest;
use App\Services\Audit\MonitoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TindakLanjutApiController extends BaseApiController
{
    public function __construct(
        protected MonitoringService $monitoringService
    ) {}

    /**
     * Daftar nomor surat tugas (untuk select/dropdown).
     */
    public function selectNomorSuratTugas(Request $request): JsonResponse
    {
        $result = $this->monitoringService->getSelectNomorSuratTugasList(
            null, // userAreaId — ditentukan oleh middleware
            $request->input('search'),
            $request->input('jenis_audit')
        );

        return $this->success($result);
    }

    /**
     * Data pemantauan berdasarkan nomor surat tugas.
     */
    public function pemantauanIndex(Request $request): JsonResponse
    {
        if (! $request->filled('nomor_surat_tugas')) {
            return $this->error('Parameter nomor_surat_tugas diperlukan.', 422);
        }

        $result = $this->monitoringService->getPemantauanData(
            $request->get('nomor_surat_tugas'),
            null, // userAreaId
            $request->input('bulan')
        );

        return $this->success($result);
    }

    /**
     * Detail tindak lanjut rekomendasi.
     */
    public function tindakLanjutDetail(int $id): JsonResponse
    {
        $rekomendasi = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'tindakLanjut',
        ])->find($id);

        if (! $rekomendasi) {
            return $this->error('Rekomendasi tidak ditemukan.', 404);
        }

        $tindakLanjut = $rekomendasi->tindakLanjut()->orderBy('created_at', 'desc')->first();

        return $this->success([
            'rekomendasi'   => $rekomendasi,
            'tindak_lanjut' => $tindakLanjut,
        ]);
    }

    /**
     * Update status rekomendasi (approve/reject).
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $rekomendasi = PenutupLhaRekomendasi::find($id);
        if (! $rekomendasi) {
            return $this->error('Rekomendasi tidak ditemukan.', 404);
        }

        $request->validate(['action' => 'required|in:approve,reject']);

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $rekomendasi,
            $request->action,
            $request->rejection_reason
        );

        if ($result['success']) {
            $rekomendasi->refresh();
            return $this->success([
                'new_status'      => $rekomendasi->status_tindak_lanjut,
                'status_approval' => $rekomendasi->status_approval,
            ], $result['message']);
        }

        return $this->error($result['message'], 403);
    }

    /**
     * Edit rekomendasi pemantauan.
     */
    public function editPemantauan(UpdatePemantauanRekomendasiRequest $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = PenutupLhaRekomendasi::find($id);
        if (! $item) {
            return $this->error('Rekomendasi tidak ditemukan.', 404);
        }

        $item->update($request->validated());

        return $this->success($item->fresh(), 'Rekomendasi berhasil diupdate.');
    }

    /**
     * Monitoring tindak lanjut — overview.
     */
    public function monitoringIndex(Request $request): JsonResponse
    {
        $data = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
        ])
        ->whereIn('status_tindak_lanjut', ['open', 'on_progress'])
        ->get();

        return $this->success($data);
    }

    /**
     * Progress tindak lanjut — overview.
     */
    public function progressIndex(Request $request): JsonResponse
    {
        $data = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'tindakLanjut',
        ])
        ->get()
        ->groupBy('status_tindak_lanjut');

        return $this->success($data);
    }

    /**
     * Persetujuan — daftar item menunggu approval.
     */
    public function persetujuanIndex(): JsonResponse
    {
        $pendingItems = collect();

        // Collect pending items from various models
        $models = [
            'walkthrough'  => \App\Models\WalkthroughAudit::class,
            'entry_meeting'=> \App\Models\EntryMeeting::class,
            'tod_bpm'      => \App\Models\TodBpmAudit::class,
            'toe'          => \App\Models\ToeAudit::class,
        ];

        foreach ($models as $type => $modelClass) {
            if (class_exists($modelClass)) {
                $items = $modelClass::where('status_approval', 'pending')->get();
                foreach ($items as $item) {
                    $pendingItems->push([
                        'type'       => $type,
                        'id'         => $item->id,
                        'created_at' => $item->created_at,
                        'data'       => $item,
                    ]);
                }
            }
        }

        return $this->success($pendingItems->sortByDesc('created_at')->values());
    }

    /**
     * Proses persetujuan.
     */
    public function persetujuanProses(Request $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $request->validate([
            'type'   => 'required|string',
            'id'     => 'required|integer',
            'action' => 'required|in:approve,reject',
        ]);

        $modelMap = [
            'walkthrough'   => \App\Models\WalkthroughAudit::class,
            'entry_meeting' => \App\Models\EntryMeeting::class,
            'tod_bpm'       => \App\Models\TodBpmAudit::class,
            'toe'           => \App\Models\ToeAudit::class,
        ];

        $modelClass = $modelMap[$request->type] ?? null;
        if (! $modelClass) {
            return $this->error('Tipe item tidak valid.', 422);
        }

        $item = $modelClass::find($request->id);
        if (! $item) {
            return $this->error('Item tidak ditemukan.', 404);
        }

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $item,
            $request->action,
            $request->input('rejection_reason')
        );

        return $result['success']
            ? $this->success($item->fresh(), $result['message'])
            : $this->error($result['message'], 403);
    }
}
