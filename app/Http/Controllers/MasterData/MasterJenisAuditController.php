<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterJenisAudit;
use Illuminate\Http\Request;
use App\Http\Requests\MasterData\StoreMasterJenisAuditRequest;
use App\Http\Requests\MasterData\UpdateMasterJenisAuditRequest;

use App\Services\MasterData\MasterJenisAuditService;

class MasterJenisAuditController extends Controller
{
    protected $jenisAuditService;

    public function __construct(MasterJenisAuditService $jenisAuditService)
    {
        $this->jenisAuditService = $jenisAuditService;
    }

    public function index()
    {
        $data = MasterJenisAudit::all();
        return view('master-data.jenis-audit.index', compact('data'));
    }

    public function create()
    {
        return view('master-data.jenis-audit.create');
    }

    public function store(StoreMasterJenisAuditRequest $request)
    {
        $this->jenisAuditService->create($request->validated());

        return redirect()->route('master.jenis-audit.index')->with('success', 'Jenis Audit berhasil ditambahkan!');
    }

    public function edit(MasterJenisAudit $masterJenisAudit)
    {
        return view('master-data.jenis-audit.edit', compact('masterJenisAudit'));
    }

    public function update(UpdateMasterJenisAuditRequest $request, MasterJenisAudit $masterJenisAudit)
    {
        $this->jenisAuditService->update($masterJenisAudit, $request->validated());

        return redirect()->route('master.jenis-audit.index')->with('success', 'Jenis Audit berhasil diperbarui!');
    }

    public function destroy(MasterJenisAudit $masterJenisAudit)
    {
        try {
            $this->jenisAuditService->delete($masterJenisAudit);
            return redirect()->route('master.jenis-audit.index')->with('success', 'Jenis Audit berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master.jenis-audit.index')->with('error', 'Data tidak bisa dihapus karena masih digunakan di data lain.');
            }
            throw $e;
        }
    }
}

