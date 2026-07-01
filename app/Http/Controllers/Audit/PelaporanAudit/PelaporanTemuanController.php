<?php

namespace App\Http\Controllers\Audit\PelaporanAudit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Audit\PelaporanAudit\StorePelaporanTemuanRequest;
use App\Http\Requests\Audit\PelaporanAudit\UpdatePelaporanTemuanRequest;
use App\Http\Requests\Audit\PelaporanAudit\ApprovalRequest;
use App\Models\Models\Audit\PelaporanTemuan;
use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\MasterData\MasterKodeAoi;
use App\Models\MasterData\MasterKodeRisk;
use App\Services\Audit\PelaporanHasilAuditService;
use App\Helpers\QueryHelper;

class PelaporanTemuanController extends Controller
{
    protected $pelaporanService;

    public function __construct(PelaporanHasilAuditService $pelaporanService)
    {
        $this->pelaporanService = $pelaporanService;
    }

    public function index(Request $request)
    {
        $query = PelaporanTemuan::with(['pelaporanHasilAudit', 'kodeAoi', 'kodeRisk']);
        
        // Apply filters
        if ($request->filled('pelaporan')) {
            $pelaporanSearch = QueryHelper::escapeLike($request->pelaporan);
            $query->whereHas('pelaporanHasilAudit', function($q) use ($pelaporanSearch) {
                $q->where('nomor_lha_lhk', 'like', '%' . $pelaporanSearch . '%');
            });
        }
        
        if ($request->filled('kode_aoi')) {
            $aoiSearch = QueryHelper::escapeLike($request->kode_aoi);
            $query->whereHas('kodeAoi', function($q) use ($aoiSearch) {
                $q->where('kode_area_of_improvement', 'like', '%' . $aoiSearch . '%')
                  ->orWhere('deskripsi_area_of_improvement', 'like', '%' . $aoiSearch . '%');
            });
        }
        
        if ($request->filled('kode_risk')) {
            $riskSearch = QueryHelper::escapeLike($request->kode_risk);
            $query->whereHas('kodeRisk', function($q) use ($riskSearch) {
                $q->where('kode_risiko', 'like', '%' . $riskSearch . '%')
                  ->orWhere('deskripsi_risiko', 'like', '%' . $riskSearch . '%');
            });
        }
        
        if ($request->filled('tahun')) {
            $query->where('tahun', 'like', '%' . QueryHelper::escapeLike($request->tahun) . '%');
        }
        
        $data = $query->get();
        return view('audit.pelaporan.temuan.index', compact('data'));
    }

    public function create(Request $request)
    {
        $returnUrl = $request->query('return_url');
        $pelaporanList = PelaporanHasilAudit::all();
        $kodeAoi = MasterKodeAoi::all();
        $kodeRisk = MasterKodeRisk::all();
        $selectedPelaporan = $request->pelaporan_hasil_audit_id ?? null;
        return view('audit.pelaporan.temuan.create', compact('pelaporanList', 'kodeAoi', 'kodeRisk', 'selectedPelaporan', 'returnUrl'));
    }

    public function store(StorePelaporanTemuanRequest $request)
    {
        // Standardise using service
        $this->pelaporanService->storeTemuan($request->validated());
        return redirect()->route('audit.pelaporan-hasil-audit.index')->with('success', 'Data temuan audit berhasil disimpan!');
    }

    public function edit($id, Request $request)
    {
        $returnUrl = $request->query('return_url');
        $item = PelaporanTemuan::findOrFail($id);
        $pelaporanList = PelaporanHasilAudit::all();
        $kodeAoi = MasterKodeAoi::all();
        $kodeRisk = MasterKodeRisk::all();
        return view('audit.pelaporan.temuan.edit', compact('item', 'pelaporanList', 'kodeAoi', 'kodeRisk', 'returnUrl'));
    }

    public function update(UpdatePelaporanTemuanRequest $request, $id)
    {
        // Use App\Models\Audit\PelaporanTemuan inside the service, but find/retrieve here
        $item = \App\Models\Audit\PelaporanTemuan::findOrFail($id);
        $this->pelaporanService->updateTemuan($item, $request->validated());
        return redirect()->route('audit.pelaporan-hasil-audit.index')->with('success', 'Data temuan audit berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = \App\Models\Audit\PelaporanTemuan::findOrFail($id);
        $this->pelaporanService->deleteTemuan($item);
        return redirect()->route('audit.pelaporan-hasil-audit.index')->with('success', 'Data temuan audit berhasil dihapus!');
    }

    public function approval($id, ApprovalRequest $request)
    {
        $item = PelaporanTemuan::findOrFail($id);
        $result = $this->pelaporanService->approveTemuan(
            $item,
            $request->action,
            $request->rejection_reason ?? $request->alasan_reject ?? null
        );
        return redirect()->back()->with('success', $result['message']);
    }
}
