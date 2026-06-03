<?php

namespace App\Http\Controllers\Audit\PerencanaanAudit;

use App\Http\Controllers\Controller;
use App\Models\Models\Audit\JadwalPkptAudit;
use App\Models\MasterData\MasterAuditee;
use Illuminate\Http\Request;
use App\Http\Requests\Audit\PerencanaanAudit\StoreJadwalPkptRequest;
use App\Http\Requests\Audit\PerencanaanAudit\UpdateJadwalPkptRequest;
use App\Services\Audit\PerencanaanAuditService;

class JadwalPkptAuditController extends Controller
{
    protected $perencanaanService;

    public function __construct(PerencanaanAuditService $perencanaanService)
    {
        $this->perencanaanService = $perencanaanService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = JadwalPkptAudit::with('auditee')->get();
        return view('audit.jadwal-pkpt-audit.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $auditees = MasterAuditee::all();
        return view('audit.jadwal-pkpt-audit.create', compact('auditees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJadwalPkptRequest $request)
    {
        $this->perencanaanService->createJadwalPkpt($request->validated());

        return redirect()->route('audit.pkpt.index')->with('success', 'Jadwal PKPT berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = JadwalPkptAudit::findOrFail($id);
        $auditees = MasterAuditee::all();
        return view('audit.jadwal-pkpt-audit.edit', compact('item', 'auditees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJadwalPkptRequest $request, $id)
    {
        $item = JadwalPkptAudit::findOrFail($id);
        $this->perencanaanService->updateJadwalPkpt($item, $request->validated());

        return redirect()->route('audit.pkpt.index')->with('success', 'Jadwal PKPT berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = JadwalPkptAudit::findOrFail($id);
        $this->perencanaanService->deleteJadwalPkpt($item);
        return redirect()->route('audit.pkpt.index')->with('success', 'Jadwal PKPT berhasil dihapus!');
    }
}
