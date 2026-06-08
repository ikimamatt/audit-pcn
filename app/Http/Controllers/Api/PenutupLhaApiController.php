<?php

namespace App\Http\Controllers\Api;

use App\Models\PenutupLhaRekomendasi;
use App\Models\PenutupLhaTindakLanjut;
use App\Services\Audit\PenutupLhaRekomendasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PenutupLhaApiController extends BaseApiController
{
    public function __construct(
        protected PenutupLhaRekomendasiService $penutupService
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

    public function store(Request $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $validated = $request->validate([
            'pelaporan_isi_lha_id' => 'required|exists:pelaporan_temuan,id',
            'rekomendasi'          => 'required|string',
            'rencana_aksi'         => 'nullable|string',
            'eviden_rekomendasi'   => 'nullable|string',
            'pic_rekomendasi'      => 'nullable|string',
            'target_waktu'         => 'nullable|date',
        ]);

        try {
            $item = $this->penutupService->create($validated);
            return $this->success($item, 'Penutup LHA Rekomendasi berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = PenutupLhaRekomendasi::find($id);
        if (! $item) {
            return $this->error('Penutup LHA Rekomendasi tidak ditemukan.', 404);
        }

        $validated = $request->validate([
            'rekomendasi'        => 'sometimes|string',
            'rencana_aksi'       => 'nullable|string',
            'eviden_rekomendasi' => 'nullable|string',
            'pic_rekomendasi'    => 'nullable|string',
            'target_waktu'       => 'nullable|date',
        ]);

        try {
            $this->penutupService->update($item, $validated);
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

    public function tindakLanjutStore(Request $request, int $rekomendasiId): JsonResponse
    {
        $rekomendasi = PenutupLhaRekomendasi::find($rekomendasiId);
        if (! $rekomendasi) {
            return $this->error('Rekomendasi tidak ditemukan.', 404);
        }

        $validated = $request->validate([
            'uraian_tindak_lanjut' => 'required|string',
            'tanggal_tindak_lanjut'=> 'required|date',
            'bukti_tindak_lanjut'  => 'nullable|string',
        ]);

        $validated['penutup_lha_rekomendasi_id'] = $rekomendasiId;

        $item = PenutupLhaTindakLanjut::create($validated);
        return $this->success($item, 'Tindak lanjut berhasil disimpan.', 201);
    }
}
