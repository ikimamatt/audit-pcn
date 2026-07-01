<?php

namespace App\Http\Controllers\Api;

use App\Models\PenutupLhaRekomendasi;
use App\Models\Audit\PerencanaanAudit;
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

        [$perPage, $page, $offset] = $this->resolvePagination($request);
        $search = $request->input('search') ?: null;
        $nomorSt = $request->input('nomor_surat_tugas');

        [$total, $rows] = $this->callSP('sp_get_pemantauan', [
            $perPage,
            $offset,
            $search,
            $nomorSt,
        ]);

        $rekomendasis = PenutupLhaRekomendasi::hydrate($rows);
        $rekomendasis->load([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'tindakLanjut',
            'picUsers'
        ]);

        $perencanaanAudit = PerencanaanAudit::where('nomor_surat_tugas', $nomorSt)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'data' => $rekomendasis,
                'perencanaanAudit' => $perencanaanAudit,
            ],
            'meta' => [
                'total'     => $total,
                'page'      => $page,
                'per_page'  => $perPage,
                'last_page' => $total > 0 ? (int) ceil($total / $perPage) : 1,
            ]
        ]);
    }

    /**
     * Detail tindak lanjut rekomendasi.
     */
    public function tindakLanjutDetail(string $id): JsonResponse
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
    public function updateStatus(Request $request, string $id): JsonResponse
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
    public function editPemantauan(UpdatePemantauanRekomendasiRequest $request, string $id): JsonResponse
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
        $selectedYear = $request->input('year', \Carbon\Carbon::now()->year);
        
        $userAreaId = null;
        $localUser = $this->localUser($request);
        if ($localUser && $this->localRole($request) === 'AUDITEE') {
            $userAreaId = $localUser->master_area_id;
        }

        $result = $this->monitoringService->getMonitoringData((int) $selectedYear, $userAreaId);

        return $this->success($result);
    }

    /**
     * Progress tindak lanjut — overview.
     */
    /**
     * Progress tindak lanjut — overview.
     * OPTIMIZED: If called without a specific status, it returns a fast count summary
     * grouped by status. If a status is requested, it returns a paginated list of recommendations.
     */
    public function progressIndex(Request $request): JsonResponse
    {
        $status = $request->input('status');

        if (!$status) {
            // Return aggregate summary (extremely fast)
            $summary = \Illuminate\Support\Facades\DB::table('penutup_lha_rekomendasi')
                ->select('status_tindak_lanjut', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
                ->groupBy('status_tindak_lanjut')
                ->pluck('total', 'status_tindak_lanjut')
                ->toArray();

            return $this->success([
                'open'        => (int) ($summary['open'] ?? 0),
                'on_progress' => (int) ($summary['on_progress'] ?? 0),
                'closed'      => (int) ($summary['closed'] ?? 0),
            ]);
        }

        // Return paginated recommendations for the specific status
        [$perPage, $page, $offset] = $this->resolvePagination($request);

        $query = PenutupLhaRekomendasi::where('status_tindak_lanjut', $status)
            ->with([
                'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
                'latestTindakLanjut'
            ]);

        $total = $query->count();
        $items = $query->limit($perPage)->offset($offset)->get();

        return $this->successPaginated($items, $total, $page, $perPage);
    }

    public function persetujuanIndex(Request $request): JsonResponse
    {
        $localUser = $this->localUser($request);
        if (!$localUser) {
            return $this->success([]);
        }

        $userId = $localUser->id;
        $isSuperAdmin = in_array(strtoupper(str_replace(' ', '', $this->localRole($request))), ['SUPERADMIN', 'SUPER_ADMIN']);

        $service = app(\App\Services\Audit\PersetujuanService::class);
        $allPendingItems = $service->getPendingItems($userId, $isSuperAdmin);

        // Search filter at collection level
        $search = $request->input('search');
        if ($search) {
            $q = strtolower($search);
            $allPendingItems = $allPendingItems->filter(function($item) use ($q) {
                return str_contains(strtolower($item['nomor_surat_tugas'] ?? ''), $q)
                    || str_contains(strtolower($item['auditee_name'] ?? ''), $q)
                    || str_contains(strtolower($item['document_name'] ?? ''), $q)
                    || str_contains(strtolower($item['title'] ?? ''), $q);
            });
        }

        $total = $allPendingItems->count();
        [$perPage, $page, $offset] = $this->resolvePagination($request);

        $paginatedItems = $allPendingItems->slice($offset, $perPage)->values();

        return response()->json([
            'success' => true,
            'data' => $paginatedItems,
            'meta' => [
                'total'     => $total,
                'page'      => $page,
                'per_page'  => $perPage,
                'last_page' => $total > 0 ? (int) ceil($total / $perPage) : 1,
            ]
        ]);
    }

    /**
     * Proses persetujuan.
     */
    public function persetujuanProses(Request $request): JsonResponse
    {
        $localUser = $this->localUser($request);
        if (!$localUser) {
            return $this->error('User lokal tidak ditemukan.', 404);
        }

        \Illuminate\Support\Facades\Auth::login($localUser);

        $request->validate([
            'type'   => 'required|string',
            'id'     => 'required|string',
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|min:10',
        ]);

        $modelType = $request->input('type');
        $id = $request->input('id');
        $action = $request->input('action');
        $reason = $request->input('rejection_reason');

        // Menyesuaikan dengan logika PersetujuanController web asli:
        // Auditee tidak boleh melakukan approval kecuali untuk tipe penutup_lha_rekomendasi
        $isAuditee = strtoupper(trim($this->localRole($request))) === 'AUDITEE';
        if ($isAuditee && $modelType !== 'penutup_lha_rekomendasi') {
            return $this->denyModify();
        }

        // Untuk non-auditee, tetap ikuti aturan general modify
        if (! $isAuditee && ! $this->canModify($request)) {
            return $this->denyModify();
        }

        $modelClass = match($modelType) {
            'pka' => \App\Models\Models\Audit\ProgramKerjaAudit::class,
            'entry_meeting' => \App\Models\EntryMeeting::class,
            'walkthrough' => \App\Models\WalkthroughAudit::class,
            'tod_bpm' => \App\Models\TodBpmAudit::class,
            'toe' => \App\Models\ToeAudit::class,
            'exit_meeting' => \App\Models\RealisasiAudit::class,
            'pelaporan_hasil_audit' => \App\Models\Models\Audit\PelaporanHasilAudit::class,
            'penutup_lha_rekomendasi' => \App\Models\PenutupLhaRekomendasi::class,
            default => null
        };

        if (! $modelClass) {
            return $this->error('Tipe dokumen tidak valid.', 422);
        }

        $item = $modelClass::find($id);
        if (! $item) {
            return $this->error('Dokumen tidak ditemukan.', 404);
        }

        if ($modelType === 'pelaporan_hasil_audit') {
            $pelaporanService = app(\App\Services\Audit\PelaporanHasilAuditService::class);
            $result = $pelaporanService->approve($item, $action, $reason);
        } elseif ($modelType === 'exit_meeting') {
            $exitMeetingService = app(\App\Services\Audit\ExitMeetingService::class);
            $result = $exitMeetingService->approve($item, $action, $reason);
        } else {
            $result = \App\Helpers\ApprovalHelper::processApproval($item, $action, $reason);
        }

        return $result['success']
            ? $this->success($item->fresh(), $result['message'])
            : $this->error($result['message'], 403);
    }
}
