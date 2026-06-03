<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterKodeAoi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\MasterData\StoreMasterKodeAoiRequest;
use App\Http\Requests\MasterData\UpdateMasterKodeAoiRequest;

use App\Services\MasterData\MasterKodeAoiService;

class MasterKodeAoiController extends Controller
{
    protected $kodeAoiService;

    public function __construct(MasterKodeAoiService $kodeAoiService)
    {
        $this->kodeAoiService = $kodeAoiService;
    }

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
    public function store(StoreMasterKodeAoiRequest $request)
    {
        $this->kodeAoiService->create($request->validated());

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
    public function update(UpdateMasterKodeAoiRequest $request, MasterKodeAoi $masterKodeAoi)
    {
        $this->kodeAoiService->update($masterKodeAoi, $request->validated());

        return redirect()->route('master.kode-aoi.index')->with('success', 'Kode AOI berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterKodeAoi $masterKodeAoi)
    {
        $this->kodeAoiService->delete($masterKodeAoi);

        return redirect()->route('master.kode-aoi.index')->with('success', 'Kode AOI berhasil dihapus!');
    }
}
