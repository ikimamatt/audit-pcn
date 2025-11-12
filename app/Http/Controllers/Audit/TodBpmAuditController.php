<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TodBpmAudit;
use App\Models\Audit\PerencanaanAudit;
use App\Models\TodBpmEvaluasi;
use Carbon\Carbon;

class TodBpmAuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Simple approach - get all data and filter in memory
        $data = TodBpmAudit::with(['perencanaanAudit', 'evaluasi'])->get();

        // Filter by month if provided
        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            
            $data = $data->filter(function($item) use ($selectedMonth) {
                if (!$item->perencanaanAudit) {
                    return false;
                }
                
                $auditStart = Carbon::parse($item->perencanaanAudit->tanggal_audit_mulai);
                return $auditStart->year == $selectedMonth->year && 
                       $auditStart->month == $selectedMonth->month;
            });
        }

        return view('audit.tod-bpm.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suratTugas = PerencanaanAudit::all();
        return view('audit.tod-bpm.create', compact('suratTugas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'judul_bpm' => 'required|string',
            'nama_bpo' => 'required|string',
            'file_bpm' => 'required|file',
            'hasil_evaluasi' => 'required|array|min:1',
            'hasil_evaluasi.*' => 'required|string',
        ]);
        $filePath = $request->file('file_bpm')->store('bpm', 'public');
        $bpm = TodBpmAudit::create([
            'perencanaan_audit_id' => $request->perencanaan_audit_id,
            'judul_bpm' => $request->judul_bpm,
            'nama_bpo' => $request->nama_bpo,
            'file_bpm' => $filePath,
        ]);
        foreach ($request->hasil_evaluasi as $hasil) {
            TodBpmEvaluasi::create([
                'tod_bpm_audit_id' => $bpm->id,
                'hasil_evaluasi' => $hasil,
            ]);
        }
        return redirect()->route('audit.tod-bpm.index')->with('success', 'BPM dan hasil evaluasi berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = TodBpmAudit::with(['perencanaanAudit', 'evaluasi'])->findOrFail($id);
        return view('audit.tod-bpm.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = TodBpmAudit::with('perencanaanAudit')->findOrFail($id);
        $suratTugas = PerencanaanAudit::all();
        return view('audit.tod-bpm.edit', compact('item', 'suratTugas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = TodBpmAudit::findOrFail($id);
        $request->validate([
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'judul_bpm' => 'required|string',
            'nama_bpo' => 'required|string',
        ]);
        $data = [
            'perencanaan_audit_id' => $request->perencanaan_audit_id,
            'judul_bpm' => $request->judul_bpm,
            'nama_bpo' => $request->nama_bpo,
        ];
        if ($request->hasFile('file_bpm')) {
            $data['file_bpm'] = $request->file('file_bpm')->store('bpm', 'public');
        }
        $item->update($data);
        return redirect()->route('audit.tod-bpm.index')->with('success', 'Data BPM berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = TodBpmAudit::findOrFail($id);
        $item->delete();
        return redirect()->route('audit.tod-bpm.index')->with('success', 'Data BPM berhasil dihapus!');
    }

    public function approval($id, Request $request)
    {
        $item = TodBpmAudit::findOrFail($id);
        
        if ($request->action == 'approve') {
            $item->status_approval = 'approved';
            $item->approved_by = auth()->id();
            $item->approved_at = now();
            $item->save();
            return redirect()->back()->with('success', 'TOD BPM berhasil diapprove!');
        } elseif ($request->action == 'reject') {
            // Validasi alasan penolakan
            $request->validate([
                'rejection_reason' => 'required|string|min:10',
            ], [
                'rejection_reason.required' => 'Alasan penolakan harus diisi',
                'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter',
            ]);

            $item->status_approval = 'rejected';
            $item->approved_by = auth()->id();
            $item->approved_at = now();
            $item->rejection_reason = $request->rejection_reason;
            $item->save();
            return redirect()->back()->with('success', 'TOD BPM berhasil ditolak dengan alasan: ' . $request->rejection_reason);
        }

        return redirect()->back()->with('error', 'Aksi tidak valid!');
    }
}
