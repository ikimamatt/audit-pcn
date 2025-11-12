<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\LhaLhkUpload;
use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\MasterData\MasterUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LhaLhkUploadController extends Controller
{
    public function index()
    {
        $data = LhaLhkUpload::with(['pelaporanHasilAudit', 'approvedBy'])->get();
        return view('audit.upload.lha-lhk.index', compact('data'));
    }

    public function create()
    {
        $data = PelaporanHasilAudit::all();
        return view('audit.unggah-dokumen.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelaporan_hasil_audit_id' => 'required|exists:pelaporan_hasil_audit,id',
            'file_lha_lhk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);
        $filePath = $request->file('file_lha_lhk')->store('lha_lhk', 'public');
        LhaLhkUpload::create([
            'pelaporan_hasil_audit_id' => $request->pelaporan_hasil_audit_id,
            'file_lha_lhk' => $filePath,
        ]);
        return redirect()->route('audit.upload.lha-lhk.index')->with('success', 'Dokumen LHA/LHK berhasil diupload!');
    }

    public function edit($id)
    {
        $lhaLhk = LhaLhkUpload::findOrFail($id);
        $lhaList = PelaporanHasilAudit::all();
        return view('audit.upload.lha-lhk.edit', compact('lhaLhk', 'lhaList'));
    }

    public function update(Request $request, $id)
    {
        $item = LhaLhkUpload::findOrFail($id);
        $request->validate([
            'pelaporan_hasil_audit_id' => 'required|exists:pelaporan_hasil_audit,id',
            'file_lha_lhk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);
        $data = [
            'pelaporan_hasil_audit_id' => $request->pelaporan_hasil_audit_id,
        ];
        if ($request->hasFile('file_lha_lhk')) {
            if ($item->file_lha_lhk) Storage::disk('public')->delete($item->file_lha_lhk);
            $data['file_lha_lhk'] = $request->file('file_lha_lhk')->store('lha_lhk', 'public');
        }
        $item->update($data);
        return redirect()->route('audit.upload.lha-lhk.index')->with('success', 'Dokumen LHA/LHK berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = LhaLhkUpload::findOrFail($id);
        if ($item->file_lha_lhk) Storage::disk('public')->delete($item->file_lha_lhk);
        $item->delete();
        return redirect()->route('audit.upload.lha-lhk.index')->with('success', 'Dokumen LHA/LHK berhasil dihapus!');
    }

    public function show($id)
    {
        $item = LhaLhkUpload::with(['pelaporanHasilAudit', 'approvedBy'])->findOrFail($id);
        return view('audit.upload.lha-lhk.show', compact('item'));
    }

    public function approval(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject'
        ]);
        $item = LhaLhkUpload::findOrFail($id);
        if ($request->action === 'approve') {
            $item->update([
                'status_approval' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);
            $message = 'Dokumen LHA/LHK berhasil diapprove!';
        } else {
            $item->update([
                'status_approval' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);
            $message = 'Dokumen LHA/LHK berhasil direject!';
        }
        return redirect()->route('audit.upload.lha-lhk.index')->with('success', $message);
    }
} 