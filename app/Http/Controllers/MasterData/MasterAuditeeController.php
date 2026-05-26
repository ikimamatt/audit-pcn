<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterAuditee;
use Illuminate\Http\Request;

class MasterAuditeeController extends Controller
{
    public function index()
    {
        $data = MasterAuditee::withCount('subBidang')->orderBy('kd_bidang')->get();
        return view('master-data.auditee.index', compact('data'));
    }

    public function create()
    {
        return view('master-data.auditee.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_bidang'           => 'required|string|max:10|unique:master_auditee,kd_bidang',
            'nama_bidang'         => 'required|string|max:255',
            'is_available_for_up' => 'nullable|boolean',
        ]);

        MasterAuditee::create([
            'kd_bidang'           => $request->kd_bidang,
            'nama_bidang'         => $request->nama_bidang,
            'is_available_for_up' => $request->boolean('is_available_for_up', true),
        ]);

        return redirect()->route('master.auditee.index')->with('success', 'Bidang berhasil ditambahkan!');
    }

    public function edit(MasterAuditee $masterAuditee)
    {
        return view('master-data.auditee.edit', compact('masterAuditee'));
    }

    public function update(Request $request, MasterAuditee $masterAuditee)
    {
        $request->validate([
            'kd_bidang'           => 'required|string|max:10|unique:master_auditee,kd_bidang,' . $masterAuditee->id,
            'nama_bidang'         => 'required|string|max:255',
            'is_available_for_up' => 'nullable|boolean',
        ]);

        $masterAuditee->update([
            'kd_bidang'           => $request->kd_bidang,
            'nama_bidang'         => $request->nama_bidang,
            'is_available_for_up' => $request->boolean('is_available_for_up', true),
        ]);

        return redirect()->route('master.auditee.index')->with('success', 'Bidang berhasil diperbarui!');
    }

    public function destroy(MasterAuditee $masterAuditee)
    {
        try {
            $masterAuditee->delete();
            return redirect()->route('master.auditee.index')->with('success', 'Bidang berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master.auditee.index')->with('error', 'Data tidak bisa dihapus karena masih digunakan di data lain.');
            }
            throw $e;
        }
    }

    /**
     * Get sub bidang data for a specific bidang (AJAX endpoint).
     */
    public function getSubBidang(MasterAuditee $masterAuditee)
    {
        $subBidang = $masterAuditee->subBidang()->orderBy('nama')->get();
        return response()->json([
            'success'    => true,
            'data'       => $subBidang,
            'bidang'     => $masterAuditee->nama_bidang,
            'bidang_id'  => $masterAuditee->id,
        ]);
    }
}