<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterArea;
use App\Models\MasterData\MasterRegion;
use Illuminate\Http\Request;
use App\Http\Requests\MasterData\StoreMasterAreaRequest;
use App\Http\Requests\MasterData\UpdateMasterAreaRequest;

use App\Services\MasterData\MasterAreaService;

class MasterAreaController extends Controller
{
    protected $areaService;

    public function __construct(MasterAreaService $areaService)
    {
        $this->areaService = $areaService;
    }

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

    public function store(StoreMasterAreaRequest $request)
    {
        $this->areaService->create($request->validated());

        return redirect()->route('master.area.index')
            ->with('success', 'Area berhasil ditambahkan!');
    }

    public function edit(MasterArea $masterArea)
    {
        $regions = MasterRegion::orderBy('kd_region')->get();
        return view('master-data.area.edit', compact('masterArea', 'regions'));
    }

    public function update(UpdateMasterAreaRequest $request, MasterArea $masterArea)
    {
        $this->areaService->update($masterArea, $request->validated());

        return redirect()->route('master.area.index')
            ->with('success', 'Area berhasil diperbarui!');
    }

    public function destroy(MasterArea $masterArea)
    {
        try {
            $this->areaService->delete($masterArea);
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
