<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterAuditee;
use Illuminate\Http\Request;

class MasterAuditeeController extends Controller
{
    public function index()
    {
        $data = MasterAuditee::all();
        return view('master-data.auditee.index', compact('data'));
    }

    public function create()
    {
        return view('master-data.auditee.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'divisi' => 'required|string|max:255',
        ]);

        MasterAuditee::create($request->only(['divisi']));

        return redirect()->route('master.auditee.index')->with('success', 'Auditee berhasil ditambahkan!');
    }

    public function edit(MasterAuditee $masterAuditee)
    {
        return view('master-data.auditee.edit', compact('masterAuditee'));
    }

    public function update(Request $request, MasterAuditee $masterAuditee)
    {
        $request->validate([
            'divisi' => 'required|string|max:255',
        ]);

        $masterAuditee->update($request->only(['divisi']));

        return redirect()->route('master.auditee.index')->with('success', 'Auditee berhasil diperbarui!');
    }

    public function destroy(MasterAuditee $masterAuditee)
    {
        try {
            $masterAuditee->delete();
            return redirect()->route('master.auditee.index')->with('success', 'Auditee berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master.auditee.index')->with('error', 'Data tidak bisa dihapus karena masih digunakan di data lain.');
            }
            throw $e;
        }
    }
} 