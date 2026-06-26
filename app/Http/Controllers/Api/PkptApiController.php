<?php

namespace App\Http\Controllers\Api;

use App\Models\Models\Audit\JadwalPkptAudit;
use App\Models\MasterData\MasterAuditee;
use App\Http\Requests\Audit\PerencanaanAudit\StoreJadwalPkptRequest;
use App\Http\Requests\Audit\PerencanaanAudit\UpdateJadwalPkptRequest;
use App\Services\Audit\PerencanaanAuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PkptApiController extends BaseApiController
{
    public function __construct(
        protected PerencanaanAuditService $perencanaanService
    ) {}

    public function index(Request $request): JsonResponse
    {
        [$perPage, $page, $offset] = $this->resolvePagination($request);

        $query = JadwalPkptAudit::with('auditee');

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function($q) use ($search) {
                $q->where('jenis_audit', 'like', $search)
                  ->orWhereHas('auditee', function($q2) use ($search) {
                      $q2->where('nama_bidang', 'like', $search);
                  });
            });
        }

        $total = $query->count();
        $data = $query->orderBy('tanggal_mulai', 'desc')
            ->limit($perPage)
            ->offset($offset)
            ->get();

        return $this->successPaginated($data, $total, $page, $perPage);
    }

    public function show(string $id): JsonResponse
    {
        $item = JadwalPkptAudit::with('auditee')->find($id);
        if (! $item) {
            return $this->error('Jadwal PKPT tidak ditemukan.', 404);
        }
        return $this->success($item);
    }

    public function store(StoreJadwalPkptRequest $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        try {
            $item = $this->perencanaanService->createJadwalPkpt($request->validated());
            return $this->success($item, 'Jadwal PKPT berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateJadwalPkptRequest $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = JadwalPkptAudit::find($id);
        if (! $item) {
            return $this->error('Jadwal PKPT tidak ditemukan.', 404);
        }

        try {
            $this->perencanaanService->updateJadwalPkpt($item, $request->validated());
            return $this->success($item->fresh(), 'Jadwal PKPT berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = JadwalPkptAudit::find($id);
        if (! $item) {
            return $this->error('Jadwal PKPT tidak ditemukan.', 404);
        }

        try {
            $this->perencanaanService->deleteJadwalPkpt($item);
            return $this->success(null, 'Jadwal PKPT berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->error('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }
}
