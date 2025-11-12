<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Models\Audit\PelaporanTemuan;
use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\MasterData\MasterKodeAoi;
use App\Models\MasterData\MasterKodeRisk;
use App\Models\MasterData\MasterUser;

class PelaporanTemuanController extends Controller
{
    public function index(Request $request)
    {
        $query = PelaporanTemuan::with(['pelaporanHasilAudit', 'kodeAoi', 'kodeRisk']);
        
        // Apply filters
        if ($request->filled('pelaporan')) {
            $query->whereHas('pelaporanHasilAudit', function($q) use ($request) {
                $q->where('nomor_lha_lhk', 'like', '%' . $request->pelaporan . '%');
            });
        }
        
        if ($request->filled('kode_aoi')) {
            $query->whereHas('kodeAoi', function($q) use ($request) {
                $q->where('kode_area_of_improvement', 'like', '%' . $request->kode_aoi . '%')
                  ->orWhere('deskripsi_area_of_improvement', 'like', '%' . $request->kode_aoi . '%');
            });
        }
        
        if ($request->filled('kode_risk')) {
            $query->whereHas('kodeRisk', function($q) use ($request) {
                $q->where('kode_risiko', 'like', '%' . $request->kode_risk . '%')
                  ->orWhere('deskripsi_risiko', 'like', '%' . $request->kode_risk . '%');
            });
        }
        
        if ($request->filled('tahun')) {
            $query->where('tahun', 'like', '%' . $request->tahun . '%');
        }
        
        $data = $query->get();
        return view('audit.pelaporan.temuan.index', compact('data'));
    }

    public function create(Request $request)
    {
        $pelaporanList = PelaporanHasilAudit::all();
        $kodeAoi = MasterKodeAoi::all();
        $kodeRisk = MasterKodeRisk::all();
        $selectedPelaporan = $request->pelaporan_hasil_audit_id ?? null;
        return view('audit.pelaporan.temuan.create', compact('pelaporanList', 'kodeAoi', 'kodeRisk', 'selectedPelaporan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelaporan_hasil_audit_id' => 'required|exists:pelaporan_hasil_audit,id',
            'hasil_temuan' => 'required|string',
            'kode_aoi_id' => 'required|exists:master_kode_aoi,id',
            'kode_risk_id' => 'required|exists:master_kode_risk,id',
            'nomor_iss' => 'required|string',
            'tahun' => 'required|digits:4',
        ]);
        PelaporanTemuan::create($request->all());
        return redirect()->route('audit.pelaporan-hasil-audit.index')->with('success', 'Data temuan audit berhasil disimpan!');
    }

    public function edit($id)
    {
        $item = PelaporanTemuan::findOrFail($id);
        $pelaporanList = PelaporanHasilAudit::all();
        $kodeAoi = MasterKodeAoi::all();
        $kodeRisk = MasterKodeRisk::all();
        return view('audit.pelaporan.temuan.edit', compact('item', 'pelaporanList', 'kodeAoi', 'kodeRisk'));
    }

    public function update(Request $request, $id)
    {
        $item = PelaporanTemuan::findOrFail($id);
        $request->validate([
            'pelaporan_hasil_audit_id' => 'required|exists:pelaporan_hasil_audit,id',
            'hasil_temuan' => 'required|string',
            'kode_aoi_id' => 'required|exists:master_kode_aoi,id',
            'kode_risk_id' => 'required|exists:master_kode_risk,id',
            'nomor_iss' => 'required|string',
            'tahun' => 'required|digits:4',
        ]);
        $item->update($request->all());
        return redirect()->route('audit.pelaporan-hasil-audit.index')->with('success', 'Data temuan audit berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = PelaporanTemuan::findOrFail($id);
        $item->delete();
        return redirect()->route('audit.pelaporan-hasil-audit.index')->with('success', 'Data temuan audit berhasil dihapus!');
    }

    public function approval($id, Request $request)
    {
        $item = PelaporanTemuan::findOrFail($id);
        if ($request->action == 'approve') {
            $item->status_approval = 'approved';
            $item->approved_by = auth()->id();
            $item->approved_at = now();
        } elseif ($request->action == 'reject') {
            $item->status_approval = 'rejected';
            $item->approved_by = auth()->id();
            $item->approved_at = now();
        }
        $item->save();
        return redirect()->back()->with('success', 'Status temuan audit berhasil diubah!');
    }
}
