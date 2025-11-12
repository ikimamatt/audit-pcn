<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TodBpmAudit;
use App\Models\TodBpmEvaluasi;

class TodBpmEvaluasiController extends Controller
{
    public function index(Request $request)
    {
        $bpm = TodBpmAudit::with('evaluasi')->findOrFail($request->tod_bpm_audit_id);
        return view('audit.tod-bpm-evaluasi.index', compact('bpm'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tod_bpm_audit_id' => 'required|exists:tod_bpm_audit,id',
            'hasil_evaluasi' => 'required|string',
        ]);
        TodBpmEvaluasi::create($request->only('tod_bpm_audit_id', 'hasil_evaluasi'));
        return redirect()->route('audit.tod-bpm-evaluasi.index', ['tod_bpm_audit_id' => $request->tod_bpm_audit_id]);
    }

    public function update(Request $request, $id)
    {
        $item = TodBpmEvaluasi::findOrFail($id);
        $request->validate([
            'hasil_evaluasi' => 'required|string',
        ]);
        $item->update(['hasil_evaluasi' => $request->hasil_evaluasi]);
        return redirect()->route('audit.tod-bpm-evaluasi.index', ['tod_bpm_audit_id' => $item->tod_bpm_audit_id]);
    }

    public function destroy(Request $request, $id)
    {
        $item = TodBpmEvaluasi::findOrFail($id);
        $bpmId = $item->tod_bpm_audit_id;
        $item->delete();
        return redirect()->route('audit.tod-bpm-evaluasi.index', ['tod_bpm_audit_id' => $bpmId]);
    }

    public function modal($bpmId)
    {
        $bpm = \App\Models\TodBpmAudit::with('evaluasi')->findOrFail($bpmId);
        return view('audit.tod-bpm-evaluasi/_modal', compact('bpm'));
    }
}
