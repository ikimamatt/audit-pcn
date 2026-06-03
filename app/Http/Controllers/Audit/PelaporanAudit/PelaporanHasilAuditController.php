<?php

namespace App\Http\Controllers\Audit\PelaporanAudit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Audit\PelaporanAudit\StorePelaporanHasilAuditRequest;
use App\Http\Requests\Audit\PelaporanAudit\UpdatePelaporanHasilAuditRequest;
use App\Http\Requests\Audit\PelaporanAudit\StorePelaporanTemuanRequest;
use App\Http\Requests\Audit\PelaporanAudit\UpdatePelaporanTemuanRequest;
use App\Http\Requests\Audit\PelaporanAudit\ApprovalRequest;
use App\Http\Requests\Audit\PelaporanAudit\GenerateNomorLhkRequest;
use App\Http\Requests\Audit\PelaporanAudit\GenerateNomorIssRequest;
use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\Audit\PerencanaanAudit;
use App\Models\Audit\PelaporanTemuan;
use App\Services\Audit\PelaporanHasilAuditService;
use App\Services\Audit\NomorGeneratorService;

class PelaporanHasilAuditController extends Controller
{
    protected $pelaporanService;
    protected $nomorService;

    public function __construct(
        PelaporanHasilAuditService $pelaporanService,
        NomorGeneratorService $nomorService
    ) {
        $this->pelaporanService = $pelaporanService;
        $this->nomorService = $nomorService;
    }

    public function index(Request $request)
    {
        $query = PelaporanHasilAudit::with(['perencanaanAudit.auditee', 'temuan.kodeAoi', 'temuan.kodeRisk'])
            ->forCurrentAuditee('perencanaanAudit');
        
        // Apply filters
        if ($request->filled('jenis_lha_lhk')) {
            $query->where('jenis_lha_lhk', $request->jenis_lha_lhk);
        }
        
        if ($request->filled('kode_spi')) {
            $query->where('kode_spi', $request->kode_spi);
        }
        
        if ($request->filled('status_approval')) {
            $query->where('status_approval', $request->status_approval);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        
        $suratTugas = \App\Models\Audit\PerencanaanAudit::with('auditee')
            ->forCurrentAuditee('self')
            ->orderBy('nomor_surat_tugas')
            ->get();
        
        $kodeAoi = \App\Models\MasterData\MasterKodeAoi::all();
        $kodeRisk = \App\Models\MasterData\MasterKodeRisk::all();
        $selectedAudit = null;
        $temuanList = collect();
        if ($request->has('audit_id')) {
            $selectedAudit = PelaporanHasilAudit::with(['temuan.kodeAoi', 'temuan.kodeRisk'])->find($request->audit_id);
            $temuanList = $selectedAudit ? $selectedAudit->temuan : collect();
        }
        return view('audit.pelaporan.index', compact('data', 'suratTugas', 'kodeAoi', 'kodeRisk', 'selectedAudit', 'temuanList'));
    }

    public function create()
    {
        if (!\App\Helpers\AuthHelper::canModifyData()) {
            abort(403, 'Anda tidak memiliki akses untuk membuat pelaporan hasil audit.');
        }

        // Ambil data perencanaan audit (surat tugas)
        $suratTugas = \App\Models\Audit\PerencanaanAudit::with('auditee')->orderBy('nomor_surat_tugas')->get();
        $kodeAoi = \App\Models\MasterData\MasterKodeAoi::all();
        $kodeRisk = \App\Models\MasterData\MasterKodeRisk::all();
        $jenisAudit = \App\Models\MasterData\MasterJenisAudit::all();
        
        $nomorLhaLhk = '';
        
        return view('audit.pelaporan.create', compact('suratTugas', 'kodeAoi', 'kodeRisk', 'nomorLhaLhk', 'jenisAudit'));
    }

    public function store(StorePelaporanHasilAuditRequest $request)
    {
        $this->pelaporanService->create($request->validated());

        return redirect()->route('audit.pelaporan-hasil-audit.index')->with('success', 'Data pelaporan hasil audit berhasil disimpan!');
    }

    public function storeTemuan(StorePelaporanTemuanRequest $request)
    {
        $this->pelaporanService->storeTemuan($request->validated());
        return redirect()->route('audit.pelaporan-hasil-audit.index', ['audit_id' => $request->pelaporan_hasil_audit_id])->with('success', 'Data temuan audit berhasil disimpan!');
    }

    public function show($id)
    {
        $item = PelaporanHasilAudit::with(['temuan.kodeAoi', 'temuan.kodeRisk', 'perencanaanAudit'])->findOrFail($id);
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAuditeeId = \App\Helpers\AuthHelper::getUserAuditeeId();
            $userUnitId = auth()->user()->master_area_id ?? null;
            $pa = $item->perencanaanAudit ?? null;
            if (!$pa || ($userAuditeeId !== null && $pa->auditee_id != $userAuditeeId) || ($userUnitId !== null && $pa->area_id != $userUnitId)) {
                abort(403, 'Anda tidak memiliki akses untuk melihat dokumen ini.');
            }
        }
        return view('audit.pelaporan.show', compact('item'));
    }

    public function edit($id)
    {
        if (!\App\Helpers\AuthHelper::canModifyData()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pelaporan hasil audit.');
        }

        $item = PelaporanHasilAudit::with(['temuan.kodeAoi', 'temuan.kodeRisk'])->findOrFail($id);
        $suratTugas = \App\Models\Audit\PerencanaanAudit::with('auditee')->orderBy('nomor_surat_tugas')->get();
        $kodeAoi = \App\Models\MasterData\MasterKodeAoi::all();
        $kodeRisk = \App\Models\MasterData\MasterKodeRisk::all();
        $jenisAudit = \App\Models\MasterData\MasterJenisAudit::all();
        return view('audit.pelaporan.edit', compact('item', 'suratTugas', 'kodeAoi', 'kodeRisk', 'jenisAudit'));
    }

    public function editTemuan($id)
    {
        $temuan = \App\Models\Audit\PelaporanTemuan::findOrFail($id);
        $kodeAoi = \App\Models\MasterData\MasterKodeAoi::all();
        $kodeRisk = \App\Models\MasterData\MasterKodeRisk::all();
        return response()->json(['temuan' => $temuan, 'kodeAoi' => $kodeAoi, 'kodeRisk' => $kodeRisk]);
    }

    public function update(UpdatePelaporanHasilAuditRequest $request, $id)
    {
        if (!\App\Helpers\AuthHelper::canModifyData()) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pelaporan hasil audit.');
        }

        $item = PelaporanHasilAudit::findOrFail($id);

        $this->pelaporanService->update($item, $request->validated());

        return redirect()->route('audit.pelaporan-hasil-audit.index')
            ->with('success', 'Data pelaporan hasil audit berhasil diupdate!');
    }

    public function updateTemuan(UpdatePelaporanTemuanRequest $request, $id)
    {
        try {
            $temuan = \App\Models\Audit\PelaporanTemuan::findOrFail($id);
            
            $this->pelaporanService->updateTemuan($temuan, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Data ISS berhasil diperbarui',
                'data' => $temuan->fresh(['kodeAoi', 'kodeRisk'])
            ]);

        } catch (\Exception $e) {
            \Log::error('updateTemuan error', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data ISS. Silakan coba lagi.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        if (!\App\Helpers\AuthHelper::canModifyData()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pelaporan hasil audit.');
        }

        $item = PelaporanHasilAudit::findOrFail($id);
        $this->pelaporanService->delete($item);
        return redirect()->route('audit.pelaporan-hasil-audit.index')->with('success', 'Data pelaporan hasil audit berhasil dihapus!');
    }

    public function destroyTemuan($id)
    {
        $temuan = \App\Models\Audit\PelaporanTemuan::findOrFail($id);
        $audit_id = $temuan->pelaporan_hasil_audit_id;
        $this->pelaporanService->deleteTemuan($temuan);
        return redirect()->route('audit.pelaporan-hasil-audit.index', ['audit_id' => $audit_id])->with('success', 'Data temuan audit berhasil dihapus!');
    }

    public function approval($id, ApprovalRequest $request)
    {
        $item = PelaporanHasilAudit::with(['temuan'])->findOrFail($id);

        $result = $this->pelaporanService->approve(
            $item,
            $request->action,
            $request->rejection_reason ?? $request->alasan_reject ?? null
        );

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function generateNomorLhk(GenerateNomorLhkRequest $request)
    {
        $res = $this->nomorService->generateNomorLhaLhk(
            $request->jenis_lha_lhk,
            $request->jenis_audit_id,
            $request->kode_spi
        );
        
        return response()->json($res);
    }

    public function generateNomorIss(GenerateNomorIssRequest $request)
    {
        $res = $this->nomorService->generateNomorIss(
            $request->kode_aoi_id,
            $request->kode_risk_id,
            $request->kode_spi ?? 'SPI.01.02'
        );
        
        return response()->json($res);
    }

    public function getTemuanData($id)
    {
        try {
            $pelaporan = PelaporanHasilAudit::with([
                'temuan.kodeAoi', 
                'temuan.kodeRisk',
                'perencanaanAudit'
            ])->findOrFail($id);
            
            if (\App\Helpers\AuthHelper::isAuditee()) {
                $userAuditeeId = \App\Helpers\AuthHelper::getUserAuditeeId();
                $userUnitId = auth()->user()->master_area_id ?? null;
                $pa = $pelaporan->perencanaanAudit ?? null;
                if (!$pa || ($userAuditeeId !== null && $pa->auditee_id != $userAuditeeId) || ($userUnitId !== null && $pa->area_id != $userUnitId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk melihat temuan ini.'
                    ], 403);
                }
            }
            
            $temuanData = $pelaporan->temuan->map(function($temuan) {
                return [
                    'id' => $temuan->id,
                    'nomor_urut_iss' => $temuan->nomor_urut_iss,
                    'nomor_iss' => $temuan->nomor_iss,
                    'hasil_temuan' => $temuan->hasil_temuan,
                    'permasalahan' => $temuan->permasalahan,
                    'penyebab' => $temuan->penyebab,
                    'kriteria' => $temuan->kriteria,
                    'dampak_terjadi' => $temuan->dampak_terjadi,
                    'dampak_potensi' => $temuan->dampak_potensi,
                    'signifikan' => $temuan->signifikan,
                    'status_approval' => $temuan->status_approval,
                    'kode_aoi_id' => $temuan->kode_aoi_id,
                    'kode_risk_id' => $temuan->kode_risk_id,
                    'kode_aoi' => [
                        'kode_area_of_improvement' => $temuan->kodeAoi->kode_area_of_improvement ?? null,
                        'deskripsi_area_of_improvement' => $temuan->kodeAoi->deskripsi_area_of_improvement ?? null,
                    ],
                    'kode_risk' => [
                        'kode_risiko' => $temuan->kodeRisk->kode_risiko ?? null,
                        'deskripsi_risiko' => $temuan->kodeRisk->deskripsi_risiko ?? null,
                    ],
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $temuanData,
                'pelaporan' => [
                    'id' => $pelaporan->id,
                    'nomor_lha_lhk' => $pelaporan->nomor_lha_lhk,
                    'jenis_lha_lhk' => $pelaporan->jenis_lha_lhk,
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('getTemuanData error', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data temuan. Silakan coba lagi.'
            ], 500);
        }
    }

    public function getTemuanById($id)
    {
        try {
            $temuan = \App\Models\Audit\PelaporanTemuan::with([
                'kodeAoi', 
                'kodeRisk', 
                'pelaporanHasilAudit.perencanaanAudit'
            ])->findOrFail($id);
            
            if (\App\Helpers\AuthHelper::isAuditee()) {
                $userAuditeeId = \App\Helpers\AuthHelper::getUserAuditeeId();
                $userUnitId = auth()->user()->master_area_id ?? null;
                $pa = $temuan->pelaporanHasilAudit->perencanaanAudit ?? null;
                if (!$pa || ($userAuditeeId !== null && $pa->auditee_id != $userAuditeeId) || ($userUnitId !== null && $pa->area_id != $userUnitId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk melihat temuan ini.'
                    ], 403);
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $temuan->id,
                    'hasil_temuan' => $temuan->hasil_temuan,
                    'permasalahan' => $temuan->permasalahan,
                    'penyebab' => $temuan->penyebab,
                    'kode_aoi_id' => $temuan->kode_aoi_id,
                    'kode_risk_id' => $temuan->kode_risk_id,
                    'kriteria' => $temuan->kriteria,
                    'dampak_terjadi' => $temuan->dampak_terjadi,
                    'dampak_potensi' => $temuan->dampak_potensi,
                    'signifikan' => $temuan->signifikan,
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('getTemuanById error', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data temuan. Silakan coba lagi.'
            ], 500);
        }
    }

    public function getAllTemuanForPenutup()
    {
        try {
            $temuanList = \App\Models\Audit\PelaporanTemuan::with(['pelaporanHasilAudit'])
                ->where('status_approval', 'approved')
                ->get();
            
            $mappedData = $temuanList->map(function($temuan) {
                return [
                    'id' => $temuan->id,
                    'nomor_iss' => $temuan->nomor_iss,
                    'hasil_temuan' => $temuan->hasil_temuan,
                    'permasalahan' => $temuan->permasalahan,
                    'pelaporan_hasil_audit' => [
                        'id' => $temuan->pelaporanHasilAudit->id,
                        'nomor_lha_lhk' => $temuan->pelaporanHasilAudit->nomor_lha_lhk,
                        'jenis_lha_lhk' => $temuan->pelaporanHasilAudit->jenis_lha_lhk,
                    ]
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $mappedData
            ]);
            
        } catch (\Exception $e) {
            \Log::error('getAllTemuanForPenutup error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data temuan. Silakan coba lagi.'
            ], 500);
        }
    }
}
