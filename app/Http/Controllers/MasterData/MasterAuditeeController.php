<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterAuditee;
use Illuminate\Http\Request;
use App\Http\Requests\MasterData\StoreMasterAuditeeRequest;
use App\Http\Requests\MasterData\UpdateMasterAuditeeRequest;

use App\Services\MasterData\MasterAuditeeService;

class MasterAuditeeController extends Controller
{
    protected $auditeeService;

    public function __construct(MasterAuditeeService $auditeeService)
    {
        $this->auditeeService = $auditeeService;
    }

    public function index()
    {
        $data = MasterAuditee::withCount('subBidang')->orderBy('kd_bidang')->get();
        return view('master-data.auditee.index', compact('data'));
    }

    public function create()
    {
        return view('master-data.auditee.create');
    }

    public function store(StoreMasterAuditeeRequest $request)
    {
        $this->auditeeService->create($request->validated());

        return redirect()->route('master.auditee.index')->with('success', 'Bidang berhasil ditambahkan!');
    }

    public function edit(MasterAuditee $masterAuditee)
    {
        return view('master-data.auditee.edit', compact('masterAuditee'));
    }

    public function update(UpdateMasterAuditeeRequest $request, MasterAuditee $masterAuditee)
    {
        $this->auditeeService->update($masterAuditee, $request->validated());

        return redirect()->route('master.auditee.index')->with('success', 'Bidang berhasil diperbarui!');
    }

    public function destroy(MasterAuditee $masterAuditee)
    {
        try {
            $this->auditeeService->delete($masterAuditee);
            return redirect()->route('master.auditee.index')->with('success', 'Bidang berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master.auditee.index')->with('error', 'Data tidak bisa dihapus karena masih digunakan di data lain.');
            }
            throw $e;
        }
    }

    /**
     * Get sub bidang data for a specific bidang (AJAX endpoint).
     */
    public function getSubBidang(MasterAuditee $masterAuditee)
    {
        $subBidang = $this->auditeeService->getSubBidang($masterAuditee);
        return response()->json([
            'success'    => true,
            'data'       => $subBidang,
            'bidang'     => $masterAuditee->nama_bidang,
            'bidang_id'  => $masterAuditee->id,
        ]);
    }
}