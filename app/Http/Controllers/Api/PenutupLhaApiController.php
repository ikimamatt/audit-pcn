<?php

namespace App\Http\Controllers\Api;

use App\Models\PenutupLhaRekomendasi;
use App\Models\PenutupLhaTindakLanjut;
use App\Http\Requests\Audit\PelaporanAudit\StorePenutupLhaRekomendasiRequest;
use App\Http\Requests\Audit\PelaporanAudit\UpdatePenutupLhaRekomendasiRequest;
use App\Http\Requests\Audit\TindakLanjut\StoreTindakLanjutRequest;
use App\Services\Audit\PenutupLhaRekomendasiService;
use App\Services\Audit\TindakLanjutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PenutupLhaApiController extends BaseApiController
{
    public function __construct(
        protected PenutupLhaRekomendasiService $penutupService,
        protected TindakLanjutService $tindakLanjutService
    ) {}

    public function index(Request $request): JsonResponse
    {
        [$perPage, $page, $offset] = $this->resolvePagination($request);

        $search = $request->input('search') ?: null;
        $status = $request->input('status') ?: null;
        $nomorSt = $request->input('nomor_surat_tugas') ?: null;

        if ($nomorSt) {
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

            return $this->successPaginated($rekomendasis, $total, $page, $perPage);
        }

        [$total, $rows] = $this->callSP('sp_get_penutup_lha', [
            $perPage,
            $offset,
            $search,
            $status,
        ]);

        return $this->successPaginated($rows, $total, $page, $perPage);
    }


    public function selectNomorSuratTugas(Request $request): JsonResponse
    {
        [$perPage, $page, $offset] = $this->resolvePagination($request);

        $localUser = $this->localUser($request);
        $userAreaId = null;
        if ($localUser && $this->localRole($request) === 'AUDITEE') {
            $userAreaId = $localUser->master_area_id;
        }

        $query = \Illuminate\Support\Facades\DB::table('perencanaan_audit as pa')
            ->join('pelaporan_hasil_audit as pha', 'pa.id', '=', 'pha.perencanaan_audit_id')
            ->join('pelaporan_temuan as pt', function ($join) {
                $join->on('pha.id', '=', 'pt.pelaporan_hasil_audit_id')
                     ->where('pt.status_approval', '=', 'approved');
            })
            ->select(
                'pa.id as perencanaan_audit_id',
                'pa.nomor_surat_tugas',
                'pa.jenis_audit',
                \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT pt.id) as count_temuan'),
                \Illuminate\Support\Facades\DB::raw('GROUP_CONCAT(DISTINCT pha.nomor_lha_lhk SEPARATOR ", ") as nomor_lha_lhk')
            )
            ->groupBy('pa.id', 'pa.nomor_surat_tugas', 'pa.jenis_audit');

        if ($userAreaId !== null) {
            $query->where('pa.area_id', $userAreaId);
        }

        if ($request->filled('jenis_audit')) {
            $query->where('pa.jenis_audit', $request->jenis_audit);
        }

        if ($request->filled('search')) {
            $search = \App\Helpers\QueryHelper::escapeLike($request->search);
            $query->where(function($q) use ($search) {
                $q->where('pa.nomor_surat_tugas', 'like', '%' . $search . '%')
                  ->orWhere('pha.nomor_lha_lhk', 'like', '%' . $search . '%');
            });
        }

        $totalQuery = clone $query;
        $total = \Illuminate\Support\Facades\DB::table(\Illuminate\Support\Facades\DB::raw("({$totalQuery->toSql()}) as sub"))
            ->mergeBindings($totalQuery)
            ->count();

        $nomorSuratTugasList = $query->orderBy('pa.nomor_surat_tugas')
            ->limit($perPage)
            ->offset($offset)
            ->get()
            ->map(function($row) {
                return [
                    'nomor_surat_tugas'    => $row->nomor_surat_tugas,
                    'perencanaan_audit_id' => $row->perencanaan_audit_id,
                    'jenis_audit'          => $row->jenis_audit,
                    'nomor_lha_lhk'        => $row->nomor_lha_lhk ?? '',
                    'count_temuan'         => (int) $row->count_temuan,
                ];
            })
            ->values();

        // Get audit types
        $jenisAuditQuery = \Illuminate\Support\Facades\DB::table('perencanaan_audit as pa')
            ->join('pelaporan_hasil_audit as pha', 'pa.id', '=', 'pha.perencanaan_audit_id')
            ->join('pelaporan_temuan as pt', function ($join) {
                $join->on('pha.id', '=', 'pt.pelaporan_hasil_audit_id')
                     ->where('pt.status_approval', '=', 'approved');
            });

        if ($userAreaId !== null) {
            $jenisAuditQuery->where('pa.area_id', $userAreaId);
        }

        $jenisAuditList = $jenisAuditQuery
            ->distinct()
            ->orderBy('pa.jenis_audit')
            ->pluck('pa.jenis_audit')
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'nomorSuratTugasList' => $nomorSuratTugasList,
                'jenisAuditList'      => $jenisAuditList,
            ],
            'meta' => [
                'total'     => $total,
                'page'      => $page,
                'per_page'  => $perPage,
                'last_page' => $total > 0 ? (int) ceil($total / $perPage) : 1,
            ]
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $item = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'tindakLanjut',
            'picUsers',
        ])->find($id);

        if (! $item) {
            return $this->error('Penutup LHA Rekomendasi tidak ditemukan.', 404);
        }

        return $this->success($item);
    }

    public function store(StorePenutupLhaRekomendasiRequest $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        try {
            $item = $this->penutupService->create($request->validated());
            return $this->success($item, 'Penutup LHA Rekomendasi berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdatePenutupLhaRekomendasiRequest $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = PenutupLhaRekomendasi::find($id);
        if (! $item) {
            return $this->error('Penutup LHA Rekomendasi tidak ditemukan.', 404);
        }

        try {
            $this->penutupService->update($item, $request->validated());
            return $this->success($item->fresh(), 'Penutup LHA Rekomendasi berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = PenutupLhaRekomendasi::find($id);
        if (! $item) {
            return $this->error('Penutup LHA Rekomendasi tidak ditemukan.', 404);
        }

        try {
            $this->penutupService->delete($item);
            return $this->success(null, 'Penutup LHA Rekomendasi berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->error('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }

    public function approval(Request $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = PenutupLhaRekomendasi::find($id);
        if (! $item) {
            return $this->error('Penutup LHA Rekomendasi tidak ditemukan.', 404);
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

    // ── Tindak Lanjut ────────────────────────────────────────────
    public function tindakLanjutIndex(string $rekomendasiId): JsonResponse
    {
        $rekomendasi = PenutupLhaRekomendasi::with(['tindakLanjut'])->find($rekomendasiId);
        if (! $rekomendasi) {
            return $this->error('Rekomendasi tidak ditemukan.', 404);
        }

        return $this->success([
            'rekomendasi'   => $rekomendasi,
            'tindak_lanjut' => $rekomendasi->tindakLanjut()->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function tindakLanjutStore(StoreTindakLanjutRequest $request, string $rekomendasiId): JsonResponse
    {
        $rekomendasi = PenutupLhaRekomendasi::find($rekomendasiId);
        if (! $rekomendasi) {
            return $this->error('Rekomendasi tidak ditemukan.', 404);
        }

        $data = $request->validated();
        if ($request->hasFile('file_eviden')) {
            $data['file_eviden_file'] = $request->file('file_eviden');
        }

        try {
            $item = $this->tindakLanjutService->storeTindakLanjut($rekomendasiId, $data);
            return $this->success($item, 'Tindak lanjut berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get active reminders for the logged in user.
     *
     * OPTIMIZED: Replaces 3-level eager loading chain (temuan → pelaporanHasilAudit →
     * perencanaanAudit) with a single flat JOIN query fetching only the 5 columns needed.
     */
    public function myReminders(Request $request): JsonResponse
    {
        $localUser = $this->localUser($request);
        if (!$localUser) {
            return $this->success([]);
        }
        $userId = $localUser->id;

        $reminders = \Illuminate\Support\Facades\DB::table('penutup_lha_rekomendasi as plr')
            ->join('penutup_lha_rekomendasi_pic as pic', function ($join) use ($userId) {
                $join->on('pic.penutup_lha_rekomendasi_id', '=', 'plr.id')
                     ->where('pic.master_user_id', '=', $userId)
                     ->where('pic.pic_type', '=', 'business_contact');
            })
            ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->whereIn('plr.status_tindak_lanjut', ['open', 'on_progress'])
            ->whereIn('plr.status_approval', ['approved', 'rejected', 'rejected_level1'])
            ->select(
                'plr.id',
                'plr.rekomendasi',
                'plr.target_waktu',
                'pa.nomor_surat_tugas',
                'pha.nomor_lha_lhk',
                'pt.nomor_iss'
            )
            ->get()
            ->map(fn($row) => [
                'id'                => $row->id,
                'rekomendasi'       => $row->rekomendasi,
                'target_waktu'      => $row->target_waktu,
                'nomor_surat_tugas' => $row->nomor_surat_tugas ?? '-',
                'nomor_lha_lhk'     => $row->nomor_lha_lhk ?? '-',
                'nomor_iss'         => $row->nomor_iss ?? '-',
                'link'              => "/audit/pemantauan/{$row->id}/tindak-lanjut",
            ]);

        return $this->success($reminders);
    }
}
