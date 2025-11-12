<?php

namespace App\Http\Controllers\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\Models\Audit\PkaRiskBasedAudit;
use App\Models\Models\Audit\PkaMilestone;
use App\Models\Models\Audit\PkaDokumen;
use App\Models\Audit\PerencanaanAudit;
use Illuminate\Http\Request;

class ProgramKerjaAuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ProgramKerjaAudit::with(['perencanaanAudit', 'risks', 'milestones', 'dokumen'])->get();
        return view('perencanaan-audit.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil semua surat tugas yang belum memiliki PKA
        $suratTugas = PerencanaanAudit::whereDoesntHave('programKerjaAudit')->get();
        
        return view('perencanaan-audit.create', compact('suratTugas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'tanggal_pka' => 'required|date',
            'no_pka' => 'required',
            // validasi lain sesuai kebutuhan
        ]);

        // Simpan data utama PKA
        $pka = ProgramKerjaAudit::create([
            'perencanaan_audit_id' => $request->perencanaan_audit_id,
            'tanggal_pka' => $request->tanggal_pka,
            'no_pka' => $request->no_pka,
            'informasi_umum' => $request->informasi_umum,
            'kpi_tidak_tercapai' => $request->kpi_tidak_tercapai,
            'data_awal_dokumen' => $request->data_awal_dokumen,
        ]);

        // Simpan risk based audit
        if ($request->has('risk')) {
            foreach ($request->risk as $risk) {
                PkaRiskBasedAudit::create([
                    'program_kerja_audit_id' => $pka->id,
                    'deskripsi_resiko' => $risk['deskripsi_resiko'] ?? '',
                    'penyebab_resiko' => $risk['penyebab_resiko'] ?? '',
                    'dampak_resiko' => $risk['dampak_resiko'] ?? '',
                    'pengendalian_eksisting' => $risk['pengendalian_eksisting'] ?? '',
                ]);
            }
        }

        // Simpan milestone
        if ($request->has('milestone')) {
            foreach ($request->milestone as $nama => $ms) {
                PkaMilestone::create([
                    'program_kerja_audit_id' => $pka->id,
                    'nama_milestone' => $nama,
                    'tanggal_mulai' => $ms['mulai'] ?? null,
                    'tanggal_selesai' => $ms['selesai'] ?? null,
                ]);
            }
        }

        // Simpan dokumen upload
        if ($request->hasFile('dokumen')) {
            foreach ($request->file('dokumen') as $file) {
                $path = $file->store('dokumen_pka', 'public');
                PkaDokumen::create([
                    'program_kerja_audit_id' => $pka->id,
                    'nama_dokumen' => $file->getClientOriginalName(),
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->route('audit.pka.index')->with('success', 'Program Kerja Audit berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = ProgramKerjaAudit::with(['perencanaanAudit', 'risks', 'milestones', 'dokumen'])->findOrFail($id);
        return view('perencanaan-audit.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = ProgramKerjaAudit::with(['perencanaanAudit', 'risks', 'milestones', 'dokumen'])->findOrFail($id);
        $suratTugas = PerencanaanAudit::all();
        return view('perencanaan-audit.edit', compact('item', 'suratTugas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'tanggal_pka' => 'required|date',
            'no_pka' => 'required',
        ]);

        $pka = ProgramKerjaAudit::findOrFail($id);
        $pka->update([
            'perencanaan_audit_id' => $request->perencanaan_audit_id,
            'tanggal_pka' => $request->tanggal_pka,
            'no_pka' => $request->no_pka,
            'informasi_umum' => $request->informasi_umum,
            'kpi_tidak_tercapai' => $request->kpi_tidak_tercapai,
            'data_awal_dokumen' => $request->data_awal_dokumen,
        ]);

        // Update risk: hapus semua, insert ulang (bisa dioptimasi jika perlu)
        $pka->risks()->delete();
        if ($request->has('risk')) {
            foreach ($request->risk as $risk) {
                PkaRiskBasedAudit::create([
                    'program_kerja_audit_id' => $pka->id,
                    'deskripsi_resiko' => $risk['deskripsi_resiko'] ?? '',
                    'penyebab_resiko' => $risk['penyebab_resiko'] ?? '',
                    'dampak_resiko' => $risk['dampak_resiko'] ?? '',
                    'pengendalian_eksisting' => $risk['pengendalian_eksisting'] ?? '',
                ]);
            }
        }

        // Update milestone: hapus semua, insert ulang
        $pka->milestones()->delete();
        if ($request->has('milestone')) {
            foreach ($request->milestone as $nama => $ms) {
                PkaMilestone::create([
                    'program_kerja_audit_id' => $pka->id,
                    'nama_milestone' => $nama,
                    'tanggal_mulai' => $ms['mulai'] ?? null,
                    'tanggal_selesai' => $ms['selesai'] ?? null,
                ]);
            }
        }

        // Upload dokumen baru
        if ($request->hasFile('dokumen')) {
            foreach ($request->file('dokumen') as $file) {
                $path = $file->store('dokumen_pka', 'public');
                PkaDokumen::create([
                    'program_kerja_audit_id' => $pka->id,
                    'nama_dokumen' => $file->getClientOriginalName(),
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->route('audit.pka.index')->with('success', 'Program Kerja Audit berhasil diupdate!');
    }

    // Approval dokumen
    public function approval($pkaId, $dokId, Request $request)
    {
        $dok = PkaDokumen::findOrFail($dokId);
        if ($request->action == 'approve') {
            $dok->status_approval = 'approved';
            $dok->approved_by = auth()->id();
            $dok->approved_at = now();
        } elseif ($request->action == 'reject') {
            $dok->status_approval = 'rejected';
            $dok->approved_by = auth()->id();
            $dok->approved_at = now();
        }
        $dok->save();
        return redirect()->back()->with('success', 'Status dokumen berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = ProgramKerjaAudit::findOrFail($id);
        $item->delete();
        return redirect()->route('audit.pka.index')->with('success', 'Data PKA berhasil dihapus!');
    }
}
