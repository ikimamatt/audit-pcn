<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\NotaDinasUpload;
use App\Models\Models\Audit\PelaporanHasilAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotaDinasUploadController extends Controller
{
    public function index()
    {
        $data = NotaDinasUpload::with('pelaporanHasilAudit')->get();
        return view('audit.upload.nota-dinas.index', compact('data'));
    }

    public function create()
    {
        $lhaList = PelaporanHasilAudit::all();
        return view('audit.upload.nota-dinas.create', compact('lhaList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelaporan_hasil_audit_id' => 'required|exists:pelaporan_hasil_audit,id',
            'file_ke_dirut' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'file_ke_dekom' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'file_ke_auditee' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);
        $data = [
            'pelaporan_hasil_audit_id' => $request->pelaporan_hasil_audit_id,
        ];
        if ($request->hasFile('file_ke_dirut')) {
            $data['file_ke_dirut'] = $request->file('file_ke_dirut')->store('nota_dinas/dirut', 'public');
        }
        if ($request->hasFile('file_ke_dekom')) {
            $data['file_ke_dekom'] = $request->file('file_ke_dekom')->store('nota_dinas/dekom', 'public');
        }
        if ($request->hasFile('file_ke_auditee')) {
            $data['file_ke_auditee'] = $request->file('file_ke_auditee')->store('nota_dinas/auditee', 'public');
        }
        NotaDinasUpload::create($data);
        return redirect()->route('audit.upload.nota-dinas.index')->with('success', 'Nota dinas berhasil diupload!');
    }

    public function edit($id)
    {
        $notaDinas = NotaDinasUpload::findOrFail($id);
        $lhaList = PelaporanHasilAudit::all();
        return view('audit.upload.nota-dinas.edit', compact('notaDinas', 'lhaList'));
    }

    public function update(Request $request, $id)
    {
        $item = NotaDinasUpload::findOrFail($id);
        $request->validate([
            'pelaporan_hasil_audit_id' => 'required|exists:pelaporan_hasil_audit,id',
            'file_ke_dirut' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'file_ke_dekom' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'file_ke_auditee' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);
        $data = [
            'pelaporan_hasil_audit_id' => $request->pelaporan_hasil_audit_id,
        ];
        if ($request->hasFile('file_ke_dirut')) {
            if ($item->file_ke_dirut) Storage::disk('public')->delete($item->file_ke_dirut);
            $data['file_ke_dirut'] = $request->file('file_ke_dirut')->store('nota_dinas/dirut', 'public');
        }
        if ($request->hasFile('file_ke_dekom')) {
            if ($item->file_ke_dekom) Storage::disk('public')->delete($item->file_ke_dekom);
            $data['file_ke_dekom'] = $request->file('file_ke_dekom')->store('nota_dinas/dekom', 'public');
        }
        if ($request->hasFile('file_ke_auditee')) {
            if ($item->file_ke_auditee) Storage::disk('public')->delete($item->file_ke_auditee);
            $data['file_ke_auditee'] = $request->file('file_ke_auditee')->store('nota_dinas/auditee', 'public');
        }
        $item->update($data);
        return redirect()->route('audit.upload.nota-dinas.index')->with('success', 'Nota dinas berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = NotaDinasUpload::findOrFail($id);
        if ($item->file_ke_dirut) Storage::disk('public')->delete($item->file_ke_dirut);
        if ($item->file_ke_dekom) Storage::disk('public')->delete($item->file_ke_dekom);
        if ($item->file_ke_auditee) Storage::disk('public')->delete($item->file_ke_auditee);
        $item->delete();
        return redirect()->route('audit.upload.nota-dinas.index')->with('success', 'Nota dinas berhasil dihapus!');
    }

    public function show($id)
    {
        $item = NotaDinasUpload::with('pelaporanHasilAudit')->findOrFail($id);
        return view('audit.upload.nota-dinas.show', compact('item'));
    }
} 