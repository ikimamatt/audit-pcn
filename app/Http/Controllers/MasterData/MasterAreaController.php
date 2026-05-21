<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterArea;
use App\Models\MasterData\MasterRegion;
use Illuminate\Http\Request;

class MasterAreaController extends Controller
{
    public function index()
    {
        $data = MasterArea::with('region')->orderBy('kd_area')->get();
        return view('master-data.area.index', compact('data'));
    }

    public function create()
    {
        $regions = MasterRegion::orderBy('kd_region')->get();
        return view('master-data.area.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_area' => 'required|string|max:50|unique:master_area,kd_area',
            'nama_area' => 'required|string|max:255',
            'kd_region' => 'nullable|string|max:50|exists:master_region,kd_region',
        ]);

        MasterArea::create($request->only(['kd_area', 'nama_area', 'kd_region']));

        return redirect()->route('master.area.index')
            ->with('success', 'Area berhasil ditambahkan!');
    }

    public function edit(MasterArea $masterArea)
    {
        $regions = MasterRegion::orderBy('kd_region')->get();
        return view('master-data.area.edit', compact('masterArea', 'regions'));
    }

    public function update(Request $request, MasterArea $masterArea)
    {
        $request->validate([
            'kd_area' => 'required|string|max:50|unique:master_area,kd_area,' . $masterArea->id,
            'nama_area' => 'required|string|max:255',
            'kd_region' => 'nullable|string|max:50|exists:master_region,kd_region',
        ]);

        $masterArea->update($request->only(['kd_area', 'nama_area', 'kd_region']));

        return redirect()->route('master.area.index')
            ->with('success', 'Area berhasil diperbarui!');
    }

    public function destroy(MasterArea $masterArea)
    {
        try {
            $masterArea->delete();
            return redirect()->route('master.area.index')
                ->with('success', 'Area berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master.area.index')
                    ->with('error', 'Data tidak bisa dihapus karena masih digunakan di data lain.');
            }
            throw $e;
        }
    }
}
