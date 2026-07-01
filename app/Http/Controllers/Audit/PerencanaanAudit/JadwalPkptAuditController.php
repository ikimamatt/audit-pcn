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
    public function create(Request $request)
    {
        $auditees = MasterAuditee::all();
        $returnUrl = $request->query('return_url');
        return view('audit.jadwal-pkpt-audit.create', compact('auditees', 'returnUrl'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJadwalPkptRequest $request)
    {
        $this->perencanaanService->createJadwalPkpt($request->validated());

        $returnUrl = $request->input('return_url');
        if ($returnUrl) {
            $expectedHost = parse_url(config('erp.allowed_domain'), PHP_URL_HOST);
            $actualHost = parse_url($returnUrl, PHP_URL_HOST);
            if ($expectedHost === $actualHost) {
                return redirect()->to($returnUrl)->with('success', 'Jadwal PKPT berhasil disimpan!');
            }
        }

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
    public function edit(Request $request, $id)
    {
        $item = JadwalPkptAudit::findOrFail($id);
        $auditees = MasterAuditee::all();
        $returnUrl = $request->query('return_url');
        return view('audit.jadwal-pkpt-audit.edit', compact('item', 'auditees', 'returnUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJadwalPkptRequest $request, $id)
    {
        $item = JadwalPkptAudit::findOrFail($id);
        $this->perencanaanService->updateJadwalPkpt($item, $request->validated());

        $returnUrl = $request->input('return_url');
        if ($returnUrl) {
            $expectedHost = parse_url(config('erp.allowed_domain'), PHP_URL_HOST);
            $actualHost = parse_url($returnUrl, PHP_URL_HOST);
            if ($expectedHost === $actualHost) {
                return redirect()->to($returnUrl)->with('success', 'Jadwal PKPT berhasil diupdate!');
            }
        }

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
