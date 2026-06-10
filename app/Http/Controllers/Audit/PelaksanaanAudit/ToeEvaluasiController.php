<?php

namespace App\Http\Controllers\Audit\PelaksanaanAudit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Audit\PelaksanaanAudit\StoreToeEvaluasiRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\UpdateToeEvaluasiRequest;
use App\Models\ToeAudit;
use App\Models\ToeEvaluasi;
use App\Services\Audit\ToeService;

class ToeEvaluasiController extends Controller
{
    protected $toeService;

    public function __construct(ToeService $toeService)
    {
        $this->toeService = $toeService;
    }

    public function index(Request $request)
    {
        $toe = ToeAudit::with('evaluasi')->findOrFail($request->toe_audit_id);
        return view('audit.toe-evaluasi.index', compact('toe'));
    }

    public function store(StoreToeEvaluasiRequest $request)
    {
        $this->toeService->createEvaluasi($request->validated());
        return redirect()->route('audit.toe-evaluasi.index', ['toe_audit_id' => $request->toe_audit_id]);
    }

    public function update(UpdateToeEvaluasiRequest $request, $id)
    {
        $item = ToeEvaluasi::findOrFail($id);
        $this->toeService->updateEvaluasi($item, $request->validated());
        return redirect()->route('audit.toe-evaluasi.index', ['toe_audit_id' => $item->toe_audit_id]);
    }

    public function destroy(Request $request, $id)
    {
        $item = ToeEvaluasi::findOrFail($id);
        $toeId = $item->toe_audit_id;
        $this->toeService->deleteEvaluasi($item);
        return redirect()->route('audit.toe-evaluasi.index', ['toe_audit_id' => $toeId]);
    }

    public function modal($toeId)
    {
        $toe = ToeAudit::with('evaluasi')->findOrFail($toeId);
        return view('audit.toe-evaluasi/_modal', compact('toe'));
    }
} 