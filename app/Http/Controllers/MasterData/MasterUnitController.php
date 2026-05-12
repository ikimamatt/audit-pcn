<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterUnit;
use Illuminate\Http\Request;

class MasterUnitController extends Controller
{
    public function index()
    {
        $data = MasterUnit::orderBy('kode_unit')->get();
        return view('master-data.unit.index', compact('data'));
    }

    public function create()
    {
        return view('master-data.unit.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_unit' => 'required|string|max:20|unique:master_unit,kode_unit',
            'nama_unit' => 'required|string|max:150',
        ]);

        MasterUnit::create($request->only(['kode_unit', 'nama_unit']));

        return redirect()->route('master.unit.index')
            ->with('success', 'Unit berhasil ditambahkan!');
    }

    public function edit(MasterUnit $masterUnit)
    {
        return view('master-data.unit.edit', compact('masterUnit'));
    }

    public function update(Request $request, MasterUnit $masterUnit)
    {
        $request->validate([
            'kode_unit' => 'required|string|max:20|unique:master_unit,kode_unit,' . $masterUnit->id,
            'nama_unit' => 'required|string|max:150',
        ]);

        $masterUnit->update($request->only(['kode_unit', 'nama_unit']));

        return redirect()->route('master.unit.index')
            ->with('success', 'Unit berhasil diperbarui!');
    }

    public function destroy(MasterUnit $masterUnit)
    {
        try {
            $masterUnit->delete();
            return redirect()->route('master.unit.index')
                ->with('success', 'Unit berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master.unit.index')
                    ->with('error', 'Data tidak bisa dihapus karena masih digunakan di data lain.');
            }
            throw $e;
        }
    }
}
