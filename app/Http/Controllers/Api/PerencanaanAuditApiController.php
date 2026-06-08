<?php

namespace App\Http\Controllers\Api;

use App\Models\Audit\PerencanaanAudit;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterJenisAudit;
use App\Models\MasterData\MasterArea;
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
     * Daftar semua perencanaan audit.
     */
    public function index(Request $request): JsonResponse
    {
        $data = PerencanaanAudit::with(['auditee', 'jenisAudit', 'area', 'koordinator', 'ketuaTim'])->get();

        return $this->success($data);
    }

    /**
     * Detail perencanaan audit.
     */
    public function show(int $id): JsonResponse
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
    public function store(Request $request): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $validated = $request->validate([
            'nomor_surat_tugas'   => 'required|string',
            'auditee_id'          => 'required|exists:master_auditee,id',
            'jenis_audit_id'      => 'nullable|exists:master_jenis_audit,id',
            'area_id'             => 'nullable|exists:master_area,id',
            'judul_penugasan'     => 'required|string',
            'ruang_lingkup'       => 'nullable|array',
            'tanggal_audit_mulai' => 'required|date',
            'tanggal_audit_sampai'=> 'required|date|after_or_equal:tanggal_audit_mulai',
            'periode_audit'       => 'nullable|string',
            'koordinator_id'      => 'nullable|exists:master_user,id',
            'ketua_tim_id'        => 'nullable|exists:master_user,id',
            'auditor'             => 'nullable|array',
        ]);

        try {
            $perencanaan = $this->perencanaanService->create($validated);
            return $this->success($perencanaan, 'Data perencanaan audit berhasil disimpan.', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update perencanaan audit.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        if (! $this->canModify($request)) {
            return $this->denyModify();
        }

        $item = PerencanaanAudit::find($id);
        if (! $item) {
            return $this->error('Data perencanaan audit tidak ditemukan.', 404);
        }

        $validated = $request->validate([
            'nomor_surat_tugas'   => 'sometimes|string',
            'auditee_id'          => 'sometimes|exists:master_auditee,id',
            'jenis_audit_id'      => 'nullable|exists:master_jenis_audit,id',
            'area_id'             => 'nullable|exists:master_area,id',
            'judul_penugasan'     => 'sometimes|string',
            'ruang_lingkup'       => 'nullable|array',
            'tanggal_audit_mulai' => 'sometimes|date',
            'tanggal_audit_sampai'=> 'sometimes|date',
            'periode_audit'       => 'nullable|string',
            'koordinator_id'      => 'nullable|exists:master_user,id',
            'ketua_tim_id'        => 'nullable|exists:master_user,id',
            'auditor'             => 'nullable|array',
        ]);

        try {
            $this->perencanaanService->update($item, $validated);
            return $this->success($item->fresh(), 'Data perencanaan audit berhasil diupdate.');
        } catch (\Exception $e) {
            return $this->error('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Hapus perencanaan audit.
     */
    public function destroy(Request $request, int $id): JsonResponse
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
