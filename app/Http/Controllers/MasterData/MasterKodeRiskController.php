<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterKodeRisk;
use Illuminate\Http\Request;

class MasterKodeRiskController extends Controller
{
    public function index()
    {
        $data = MasterKodeRisk::all();
        return view('master-data.kode-risk.index', compact('data'));
    }

    public function create()
    {
        return view('master-data.kode-risk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelompok_risiko' => 'required|string|max:255',
            'kode_risiko' => 'required|string|max:255|unique:master_kode_risk',
            'kelompok_risiko_detail' => 'required|string|max:255',
            'deskripsi_risiko' => 'required|string',
        ]);

        MasterKodeRisk::create($request->only([
            'kelompok_risiko',
            'kode_risiko',
            'kelompok_risiko_detail',
            'deskripsi_risiko',
        ]));

        return redirect()->route('master.kode-risk.index')->with('success', 'Kode Risk berhasil ditambahkan!');
    }

    public function edit(MasterKodeRisk $masterKodeRisk)
    {
        return view('master-data.kode-risk.edit', compact('masterKodeRisk'));
    }

    public function update(Request $request, MasterKodeRisk $masterKodeRisk)
    {
        $request->validate([
            'kelompok_risiko' => 'required|string|max:255',
            'kode_risiko' => 'required|string|max:255|unique:master_kode_risk,kode_risiko,' . $masterKodeRisk->id,
            'kelompok_risiko_detail' => 'required|string|max:255',
            'deskripsi_risiko' => 'required|string',
        ]);

        $masterKodeRisk->update($request->only([
            'kelompok_risiko',
            'kode_risiko',
            'kelompok_risiko_detail',
            'deskripsi_risiko',
        ]));

        return redirect()->route('master.kode-risk.index')->with('success', 'Kode Risk berhasil diperbarui!');
    }

    public function destroy(MasterKodeRisk $masterKodeRisk)
    {
        try {
            $masterKodeRisk->delete();
            return redirect()->route('master.kode-risk.index')->with('success', 'Kode Risk berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master.kode-risk.index')->with('error', 'Data tidak bisa dihapus karena masih digunakan di data lain.');
            }
            throw $e;
        }
    }
} 