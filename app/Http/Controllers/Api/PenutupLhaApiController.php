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
        $data = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'tindakLanjut',
        ])->get();

        return $this->success($data);
    }

    public function show(int $id): JsonResponse
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

    public function update(UpdatePenutupLhaRekomendasiRequest $request, int $id): JsonResponse
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

    public function destroy(Request $request, int $id): JsonResponse
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

    public function approval(Request $request, int $id): JsonResponse
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

    public function tindakLanjutIndex(int $rekomendasiId): JsonResponse
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

    public function tindakLanjutStore(StoreTindakLanjutRequest $request, int $rekomendasiId): JsonResponse
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
}
