<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterKodeRisk;
use Illuminate\Http\Request;
use App\Http\Requests\MasterData\StoreMasterKodeRiskRequest;
use App\Http\Requests\MasterData\UpdateMasterKodeRiskRequest;

use App\Services\MasterData\MasterKodeRiskService;

class MasterKodeRiskController extends Controller
{
    protected $kodeRiskService;

    public function __construct(MasterKodeRiskService $kodeRiskService)
    {
        $this->kodeRiskService = $kodeRiskService;
    }

    public function index()
    {
        $data = MasterKodeRisk::all();
        return view('master-data.kode-risk.index', compact('data'));
    }

    public function create()
    {
        return view('master-data.kode-risk.create');
    }

    public function store(StoreMasterKodeRiskRequest $request)
    {
        $this->kodeRiskService->create($request->validated());

        return redirect()->route('master.kode-risk.index')->with('success', 'Kode Risk berhasil ditambahkan!');
    }

    public function edit(MasterKodeRisk $masterKodeRisk)
    {
        return view('master-data.kode-risk.edit', compact('masterKodeRisk'));
    }

    public function update(UpdateMasterKodeRiskRequest $request, MasterKodeRisk $masterKodeRisk)
    {
        $this->kodeRiskService->update($masterKodeRisk, $request->validated());

        return redirect()->route('master.kode-risk.index')->with('success', 'Kode Risk berhasil diperbarui!');
    }

    public function destroy(MasterKodeRisk $masterKodeRisk)
    {
        try {
            $this->kodeRiskService->delete($masterKodeRisk);
            return redirect()->route('master.kode-risk.index')->with('success', 'Kode Risk berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master.kode-risk.index')->with('error', 'Data tidak bisa dihapus karena masih digunakan di data lain.');
            }
            throw $e;
        }
    }
} 