<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ToeAudit;
use App\Models\ToeEvaluasi;

class ToeEvaluasiController extends Controller
{
    public function index(Request $request)
    {
        $toe = ToeAudit::with('evaluasi')->findOrFail($request->toe_audit_id);
        return view('audit.toe-evaluasi.index', compact('toe'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'toe_audit_id' => 'required|exists:toe_audit,id',
            'hasil_evaluasi' => 'required|string',
        ]);
        ToeEvaluasi::create($request->only('toe_audit_id', 'hasil_evaluasi'));
        return redirect()->route('audit.toe-evaluasi.index', ['toe_audit_id' => $request->toe_audit_id]);
    }

    public function update(Request $request, $id)
    {
        $item = ToeEvaluasi::findOrFail($id);
        $request->validate([
            'hasil_evaluasi' => 'required|string',
        ]);
        $item->update(['hasil_evaluasi' => $request->hasil_evaluasi]);
        return redirect()->route('audit.toe-evaluasi.index', ['toe_audit_id' => $item->toe_audit_id]);
    }

    public function destroy(Request $request, $id)
    {
        $item = ToeEvaluasi::findOrFail($id);
        $toeId = $item->toe_audit_id;
        $item->delete();
        return redirect()->route('audit.toe-evaluasi.index', ['toe_audit_id' => $toeId]);
    }

    public function modal($toeId)
    {
        $toe = \App\Models\ToeAudit::with('evaluasi')->findOrFail($toeId);
        return view('audit.toe-evaluasi/_modal', compact('toe'));
    }
} 