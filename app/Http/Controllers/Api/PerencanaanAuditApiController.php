<?php

namespace App\Http\Controllers\Api;

use App\Models\Audit\PerencanaanAudit;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterJenisAudit;
use App\Models\MasterData\MasterArea;
use App\Http\Requests\Audit\PerencanaanAudit\StorePerencanaanRequest;
use App\Http\Requests\Audit\PerencanaanAudit\UpdatePerencanaanRequest;
use App\Services\Audit\PerencanaanAuditService;
use App\Services\Audit\NomorGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PerencanaanAuditApiController extends BaseApiController
{
    public function __construct(
        protected PerencanaanAuditService $perencanaanService,
        protected NomorGeneratorService $nomorService
    ) {}

    /**
     * Daftar perencanaan audit (server-side paginated via Stored Procedure).
     *
     * Query params:
     *   - page     : halaman aktif (default 1)
     *   - limit    : jumlah item per halaman (default 15, max 100)
     *   - search   : filter nomor_surat_tugas (LIKE)
     *   - jenis_id : filter jenis_audit_id (exact match)
     */
    public function index(Request $request): JsonResponse
    {
        [$perPage, $page, $offset] = $this->resolvePagination($request);

        $search   = $request->input('search')   ?: null;
        $jenisId  = $request->input('jenis_id') ?: null;

        [$total, $rows] = $this->callSP('sp_get_perencanaan_audit', [
            $perPage,
            $offset,
            $search,
            $jenisId,
        ]);

        // Map flat SP result ke struktur nested yang konsisten dengan response lama
        $data = array_map(function (array $row) {
            return [
                'id'                  => $row['id'],
                'nomor_surat_tugas'   => $row['nomor_surat_tugas'],
                'tanggal_surat_tugas' => $row['tanggal_surat_tugas'],
                'jenis_audit'         => $row['jenis_audit'],
                'jenis_audit_id'      => $row['jenis_audit_id'],
                'koordinator_id'      => $row['koordinator_id'],
                'ketua_tim_id'        => $row['ketua_tim_id'],
                'auditor'             => json_decode($row['auditor'] ?? '[]', true),
                'auditee_id'          => $row['auditee_id'],
                'ruang_lingkup'       => json_decode($row['ruang_lingkup'] ?? '[]', true),
                'tanggal_audit_mulai' => $row['tanggal_audit_mulai'],
                'tanggal_audit_sampai'=> $row['tanggal_audit_sampai'],
                'periode_audit'       => $row['periode_audit'],
                'area_id'             => $row['area_id'],
                'created_at'          => $row['created_at'],
                'auditee' => [
                    'nama_bidang' => $row['auditee_nama_bidang'],
                ],
                'jenis_audit_obj' => [
                    'nama_jenis_audit' => $row['jenis_audit_nama'],
                ],
                'koordinator' => $row['koordinator_nama'] ? ['nama' => $row['koordinator_nama']] : null,
                'ketua_tim'   => $row['ketua_tim_nama']   ? ['nama' => $row['ketua_tim_nama']]   : null,
            ];
        }, $rows);

        return $this->successPaginated($data, $total, $page, $perPage);
    }

    /**
     * Detail perencanaan audit.
     */
    public function show(string $id): JsonResponse
    {
        $item = PerencanaanAudit::with(['auditee', 'jenisAudit', 'area', 'koordinator', 'ketuaTim'])->find($id);

        if (! $item) {
            return $this->error('Data perencanaan audit tidak ditemukan.', 404);
        }

        return $this->success($item);
    }

    /**
     * Data referensi untuk form create/edit.
     */
    public function formData(): JsonResponse
    {
        $auditees    = MasterAuditee::all();
        $jenisAudits = MasterJenisAudit::all();
        $areas       = MasterArea::with('region')->orderBy('kd_area')->get();
        $auditors    = MasterUser::with('akses')
            ->whereDoesntHave('akses', fn($q) => $q->where('nama_akses', 'AUDITEE'))
            ->orderBy('nama')
            ->get();

        return $this->success(compact('auditees', 'jenisAudits', 'areas', 'auditors'));
    }

    /**
     * Simpan perencanaan audit baru.
     */
    public function store(StorePerencanaanRequest $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        try {
            $perencanaan = $this->perencanaanService->create($request->validated());
            return $this->success($perencanaan, 'Data perencanaan audit berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update perencanaan audit.
     */
    public function update(UpdatePerencanaanRequest $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = PerencanaanAudit::find($id);
        if (! $item) {
            return $this->error('Data perencanaan audit tidak ditemukan.', 404);
        }

        try {
            $this->perencanaanService->update($item, $request->validated());
            return $this->success($item->fresh(), 'Data perencanaan audit berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Hapus perencanaan audit.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = PerencanaanAudit::find($id);
        if (! $item) {
            return $this->error('Data perencanaan audit tidak ditemukan.', 404);
        }

        try {
            $this->perencanaanService->delete($item);
            return $this->success(null, 'Data perencanaan audit berhasil dihapus.');
        } catch (\DomainException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->error('Gagal menghapus data.', 500);
        }
    }

    /**
     * Generate nomor surat tugas otomatis.
     */
    public function getNomorSuratTugas(Request $request): JsonResponse
    {
        $jenisAudit = $request->input('jenis_audit');
        $nomor = $this->nomorService->generateNomorSuratTugas($jenisAudit);

        return $this->success(['nomor_surat_tugas' => $nomor]);
    }
}
