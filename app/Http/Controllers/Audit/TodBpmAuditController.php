<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TodBpmAudit;
use App\Models\Audit\PerencanaanAudit;
use App\Models\TodBpmEvaluasi;
use App\Models\WalkthroughAudit;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TodBpmAuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Simple approach - get all data and filter in memory
        $data = TodBpmAudit::with(['perencanaanAudit.auditee', 'evaluasi'])->get();

        // Filter by user's divisi/cabang (except for KSPI, ASMAN KSPI, Auditor)
        $userAuditeeId = \App\Helpers\AuthHelper::getUserAuditeeId();
        if ($userAuditeeId !== null) {
            $data = $data->filter(function($item) use ($userAuditeeId) {
                return $item->perencanaanAudit && $item->perencanaanAudit->auditee_id == $userAuditeeId;
            });
        }

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
        // Ambil walkthrough yang sudah memiliki file BPM
        $walkthroughs = WalkthroughAudit::whereNotNull('file_bpm')
            ->where('status_approval', 'approved')
            ->with('perencanaanAudit')
            ->get()
            ->groupBy('perencanaan_audit_id');
        
        return view('audit.tod-bpm.create', compact('suratTugas', 'walkthroughs'));
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
            'resiko' => 'nullable|string',
            'kontrol' => 'nullable|string',
            'walkthrough_id' => 'required|exists:walkthrough_audit,id',
            'file_kka_tod' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB
            'hasil_evaluasi' => 'required|array|min:1',
            'hasil_evaluasi.*' => 'required|string',
        ]);

        // Gunakan file dari walkthrough (wajib)
        $walkthrough = WalkthroughAudit::findOrFail($request->walkthrough_id);
        if (!$walkthrough->file_bpm) {
            return redirect()->back()->with('error', 'File BPM dari walkthrough tidak ditemukan! Pastikan walkthrough sudah memiliki file BPM.')->withInput();
        }
        
        $filePath = $walkthrough->file_bpm;

        // Handle upload file KKA ToD
        $fileKkaTodPath = null;
        if ($request->hasFile('file_kka_tod')) {
            $fileKkaTodPath = $request->file('file_kka_tod')->store('tod-bpm/kka-tod', 'public');
        }

        $bpm = TodBpmAudit::create([
            'perencanaan_audit_id' => $request->perencanaan_audit_id,
            'judul_bpm' => $request->judul_bpm,
            'nama_bpo' => $request->nama_bpo,
            'resiko' => $request->resiko,
            'kontrol' => $request->kontrol,
            'file_bpm' => $filePath,
            'file_kka_tod' => $fileKkaTodPath,
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
        // Ambil walkthrough yang sudah memiliki file BPM
        $walkthroughs = WalkthroughAudit::whereNotNull('file_bpm')
            ->where('status_approval', 'approved')
            ->with('perencanaanAudit')
            ->get()
            ->groupBy('perencanaan_audit_id');
        
        return view('audit.tod-bpm.edit', compact('item', 'suratTugas', 'walkthroughs'));
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
            'resiko' => 'nullable|string',
            'kontrol' => 'nullable|string',
            'walkthrough_id' => 'nullable|exists:walkthrough_audit,id',
            'file_kka_tod' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB
        ]);
        
        $data = [
            'perencanaan_audit_id' => $request->perencanaan_audit_id,
            'judul_bpm' => $request->judul_bpm,
            'nama_bpo' => $request->nama_bpo,
            'resiko' => $request->resiko,
            'kontrol' => $request->kontrol,
        ];
        
        // Jika walkthrough_id dipilih, gunakan file dari walkthrough
        if ($request->walkthrough_id) {
            $walkthrough = WalkthroughAudit::findOrFail($request->walkthrough_id);
            if ($walkthrough->file_bpm) {
                $data['file_bpm'] = $walkthrough->file_bpm;
            }
        }
        // Jika tidak dipilih, tetap gunakan file yang sudah ada
        
        // Handle upload file KKA ToD
        if ($request->hasFile('file_kka_tod')) {
            // Hapus file lama jika ada
            if ($item->file_kka_tod && Storage::disk('public')->exists($item->file_kka_tod)) {
                Storage::disk('public')->delete($item->file_kka_tod);
            }
            $data['file_kka_tod'] = $request->file('file_kka_tod')->store('tod-bpm/kka-tod', 'public');
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
        
        // Validasi alasan penolakan jika reject
        if ($request->action == 'reject') {
            $request->validate([
                'rejection_reason' => 'required|string|min:10',
            ], [
                'rejection_reason.required' => 'Alasan penolakan harus diisi',
                'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter',
            ]);
        }

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $item,
            $request->action,
            $request->rejection_reason ?? null
        );

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
