<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ToeAudit;
use App\Models\Audit\PerencanaanAudit;
use App\Models\ToeEvaluasi;
use App\Models\TodBpmAudit;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ToeAuditController extends Controller
{
    public function index(Request $request)
    {
        // Simple approach - get all data and filter in memory
        $data = ToeAudit::with(['perencanaanAudit.auditee', 'evaluasi'])->get();

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

        return view('audit.toe.index', compact('data'));
    }

    public function create()
    {
        $suratTugas = PerencanaanAudit::all();
        $bpmList = TodBpmAudit::all();
        return view('audit.toe.create', compact('suratTugas', 'bpmList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'judul_bpm' => 'required|string',
            'pengendalian_eksisting' => 'required|string',
            'pemilihan_sampel_audit' => 'nullable|string',
            'resiko' => 'nullable|string',
            'kontrol' => 'nullable|string',
            'file_kka_toe' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB
            'hasil_evaluasi' => 'required|array|min:1',
            'hasil_evaluasi.*' => 'required|string',
        ]);

        // Handle upload file KKA ToE
        $fileKkaToePath = null;
        if ($request->hasFile('file_kka_toe')) {
            $fileKkaToePath = $request->file('file_kka_toe')->store('toe/kka-toe', 'public');
        }

        $toe = ToeAudit::create([
            'perencanaan_audit_id' => $request->perencanaan_audit_id,
            'judul_bpm' => $request->judul_bpm,
            'pengendalian_eksisting' => $request->pengendalian_eksisting,
            'pemilihan_sampel_audit' => $request->pemilihan_sampel_audit,
            'resiko' => $request->resiko,
            'kontrol' => $request->kontrol,
            'file_kka_toe' => $fileKkaToePath,
        ]);
        foreach ($request->hasil_evaluasi as $hasil) {
            ToeEvaluasi::create([
                'toe_audit_id' => $toe->id,
                'hasil_evaluasi' => $hasil,
            ]);
        }
        return redirect()->route('audit.toe.index')->with('success', 'TOE dan hasil evaluasi berhasil disimpan!');
    }

    public function show($id)
    {
        $item = ToeAudit::with(['perencanaanAudit', 'evaluasi'])->findOrFail($id);
        return view('audit.toe.show', compact('item'));
    }

    public function edit($id)
    {
        $item = ToeAudit::with('perencanaanAudit')->findOrFail($id);
        $suratTugas = PerencanaanAudit::all();
        $bpmList = TodBpmAudit::all();
        return view('audit.toe.edit', compact('item', 'suratTugas', 'bpmList'));
    }

    public function update(Request $request, $id)
    {
        $item = ToeAudit::findOrFail($id);
        $request->validate([
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'judul_bpm' => 'required|string',
            'pengendalian_eksisting' => 'required|string',
            'pemilihan_sampel_audit' => 'nullable|string',
            'resiko' => 'nullable|string',
            'kontrol' => 'nullable|string',
            'file_kka_toe' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB
        ]);
        
        $data = [
            'perencanaan_audit_id' => $request->perencanaan_audit_id,
            'judul_bpm' => $request->judul_bpm,
            'pengendalian_eksisting' => $request->pengendalian_eksisting,
            'pemilihan_sampel_audit' => $request->pemilihan_sampel_audit,
            'resiko' => $request->resiko,
            'kontrol' => $request->kontrol,
        ];

        // Handle upload file KKA ToE
        if ($request->hasFile('file_kka_toe')) {
            // Hapus file lama jika ada
            if ($item->file_kka_toe && Storage::disk('public')->exists($item->file_kka_toe)) {
                Storage::disk('public')->delete($item->file_kka_toe);
            }
            $data['file_kka_toe'] = $request->file('file_kka_toe')->store('toe/kka-toe', 'public');
        }

        $item->update($data);
        return redirect()->route('audit.toe.index')->with('success', 'Data TOE berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = ToeAudit::findOrFail($id);
        $item->delete();
        return redirect()->route('audit.toe.index')->with('success', 'Data TOE berhasil dihapus!');
    }

    public function approval($id, Request $request)
    {
        $item = ToeAudit::findOrFail($id);
        
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