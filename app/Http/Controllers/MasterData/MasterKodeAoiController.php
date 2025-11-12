<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterKodeAoi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterKodeAoiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = MasterKodeAoi::all();
        return view('master-data.kode-aoi.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-data.kode-aoi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'indikator_pengawasan' => 'required|string|max:255',
            'kode_area_of_improvement' => 'required|string|max:255|unique:master_kode_aoi',
            'deskripsi_area_of_improvement' => 'required|string',
        ]);

        MasterKodeAoi::create($request->only([
            'indikator_pengawasan',
            'kode_area_of_improvement',
            'deskripsi_area_of_improvement',
        ]));

        return redirect()->route('master.kode-aoi.index')->with('success', 'Kode AOI berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterKodeAoi $masterKodeAoi)
    {
        return view('master-data.kode-aoi.edit', compact('masterKodeAoi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterKodeAoi $masterKodeAoi)
    {
        Log::info('Update MasterKodeAoi called', ['id' => $masterKodeAoi->id, 'request' => $request->all()]);
        $validated = $request->validate([
            'indikator_pengawasan' => 'required|string|max:255',
            'kode_area_of_improvement' => 'required|string|max:255|unique:master_kode_aoi,kode_area_of_improvement,' . $masterKodeAoi->id,
            'deskripsi_area_of_improvement' => 'required|string',
        ]);

        $masterKodeAoi->update($validated);
        Log::info('Update MasterKodeAoi success', ['id' => $masterKodeAoi->id, 'updated' => $masterKodeAoi->toArray()]);

        return redirect()->route('master.kode-aoi.index')->with('success', 'Kode AOI berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterKodeAoi $masterKodeAoi)
    {
        $masterKodeAoi->delete();

        return redirect()->route('master.kode-aoi.index')->with('success', 'Kode AOI berhasil dihapus!');
    }
}
