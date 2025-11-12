<?php

namespace App\Http\Controllers\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\Models\Audit\JadwalPkptAudit;
use App\Models\MasterData\MasterAuditee;
use Illuminate\Http\Request;

class JadwalPkptAuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = JadwalPkptAudit::with('auditee')->get();
        return view('audit.jadwal-pkpt-audit.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $auditees = MasterAuditee::all();
        return view('audit.jadwal-pkpt-audit.create', compact('auditees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'auditee_id' => 'required|exists:master_auditee,id',
            'jenis_audit' => 'required',
            'jumlah_auditor' => 'required|integer|min:1',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai', // Tanggal selesai harus setelah atau sama dengan tanggal mulai
        ]);

        JadwalPkptAudit::create([
            'auditee_id' => $request->auditee_id,
            'jenis_audit' => $request->jenis_audit,
            'jumlah_auditor' => $request->jumlah_auditor,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status_approval' => 'pending',
        ]);

        return redirect()->route('audit.pkpt.index')->with('success', 'Jadwal PKPT berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = JadwalPkptAudit::findOrFail($id);
        $auditees = MasterAuditee::all();
        return view('audit.jadwal-pkpt-audit.edit', compact('item', 'auditees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'auditee_id' => 'required|exists:master_auditee,id',
            'jenis_audit' => 'required',
            'jumlah_auditor' => 'required|integer|min:1',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai', // Tanggal selesai harus setelah atau sama dengan tanggal mulai
        ]);

        $item = JadwalPkptAudit::findOrFail($id);
        $item->update([
            'auditee_id' => $request->auditee_id,
            'jenis_audit' => $request->jenis_audit,
            'jumlah_auditor' => $request->jumlah_auditor,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return redirect()->route('audit.pkpt.index')->with('success', 'Jadwal PKPT berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = JadwalPkptAudit::findOrFail($id);
        $item->delete();
        return redirect()->route('audit.pkpt.index')->with('success', 'Jadwal PKPT berhasil dihapus!');
    }

    public function approval($id, Request $request)
    {
        $item = JadwalPkptAudit::findOrFail($id);
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
        return redirect()->back()->with('success', 'Status jadwal PKPT berhasil diubah!');
    }
}
