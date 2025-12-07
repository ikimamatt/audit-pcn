<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterJenisAudit;
use Illuminate\Http\Request;

class MasterJenisAuditController extends Controller
{
    public function index()
    {
        $data = MasterJenisAudit::all();
        return view('master-data.jenis-audit.index', compact('data'));
    }

    public function create()
    {
        return view('master-data.jenis-audit.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jenis_audit' => 'required|string|max:255',
            'kode' => 'nullable|string|max:255',
        ]);

        MasterJenisAudit::create($request->only(['nama_jenis_audit', 'kode']));

        return redirect()->route('master.jenis-audit.index')->with('success', 'Jenis Audit berhasil ditambahkan!');
    }

    public function edit(MasterJenisAudit $masterJenisAudit)
    {
        return view('master-data.jenis-audit.edit', compact('masterJenisAudit'));
    }

    public function update(Request $request, MasterJenisAudit $masterJenisAudit)
    {
        $request->validate([
            'nama_jenis_audit' => 'required|string|max:255',
            'kode' => 'nullable|string|max:255',
        ]);

        $masterJenisAudit->update($request->only(['nama_jenis_audit', 'kode']));

        return redirect()->route('master.jenis-audit.index')->with('success', 'Jenis Audit berhasil diperbarui!');
    }

    public function destroy(MasterJenisAudit $masterJenisAudit)
    {
        try {
            $masterJenisAudit->delete();
            return redirect()->route('master.jenis-audit.index')->with('success', 'Jenis Audit berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master.jenis-audit.index')->with('error', 'Data tidak bisa dihapus karena masih digunakan di data lain.');
            }
            throw $e;
        }
    }
}

