<?php

namespace App\Http\Controllers\Audit\PelaksanaanAudit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Audit\PelaksanaanAudit\StoreTodBpmEvaluasiRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\UpdateTodBpmEvaluasiRequest;
use App\Models\TodBpmAudit;
use App\Models\TodBpmEvaluasi;
use App\Services\Audit\TodBpmService;

class TodBpmEvaluasiController extends Controller
{
    protected $todBpmService;

    public function __construct(TodBpmService $todBpmService)
    {
        $this->todBpmService = $todBpmService;
    }

    public function index(Request $request)
    {
        $bpm = TodBpmAudit::with('evaluasi')->findOrFail($request->tod_bpm_audit_id);
        return view('audit.tod-bpm-evaluasi.index', compact('bpm'));
    }

    public function store(StoreTodBpmEvaluasiRequest $request)
    {
        $this->todBpmService->createEvaluasi($request->validated());
        return redirect()->route('audit.tod-bpm-evaluasi.index', ['tod_bpm_audit_id' => $request->tod_bpm_audit_id]);
    }

    public function update(UpdateTodBpmEvaluasiRequest $request, $id)
    {
        $item = TodBpmEvaluasi::findOrFail($id);
        $this->todBpmService->updateEvaluasi($item, $request->validated());
        return redirect()->route('audit.tod-bpm-evaluasi.index', ['tod_bpm_audit_id' => $item->tod_bpm_audit_id]);
    }

    public function destroy(Request $request, $id)
    {
        $item = TodBpmEvaluasi::findOrFail($id);
        $bpmId = $item->tod_bpm_audit_id;
        $this->todBpmService->deleteEvaluasi($item);
        return redirect()->route('audit.tod-bpm-evaluasi.index', ['tod_bpm_audit_id' => $bpmId]);
    }

    public function modal($bpmId)
    {
        $bpm = \App\Models\TodBpmAudit::with('evaluasi')->findOrFail($bpmId);
        return view('audit.tod-bpm-evaluasi/_modal', compact('bpm'));
    }
}
