<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\PenutupLhaRekomendasi;
use Illuminate\Http\Request;

class PemantauanAuditController extends Controller
{
    public function index(Request $request)
    {
        // Load data with all necessary relationships
        $query = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'tindakLanjut'
        ]);
        
        // Apply month filter if provided
        if ($request->filled('bulan')) {
            [$year, $month] = explode('-', $request->bulan);
            $query->whereYear('target_waktu', $year)->whereMonth('target_waktu', $month);
        }
        
        $data = $query->get();
        
        return view('audit.pemantauan.index', compact('data'));
    }

    public function edit($id)
    {
        $item = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee'
        ])->findOrFail($id);
        return view('audit.pemantauan.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee'
        ])->findOrFail($id);
        $request->validate([
            'rekomendasi' => 'required|string|max:5000',
            'rencana_aksi' => 'required|string|max:5000',
            'eviden_rekomendasi' => 'required|string|max:5000',
            'pic_rekomendasi' => 'required|string|max:500',
            'target_waktu' => 'required|date',
        ]);
        $item->update($request->only(['rekomendasi', 'rencana_aksi', 'eviden_rekomendasi', 'pic_rekomendasi', 'target_waktu']));
        return redirect()->route('audit.pemantauan.index')->with('success', 'Rekomendasi berhasil diupdate!');
    }

    public function tindakLanjutIndex($id)
    {
        $rekomendasi = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee'
        ])->findOrFail($id);
        $tindakLanjut = $rekomendasi->tindakLanjut()->orderBy('created_at', 'desc')->first();
        return view('audit.pemantauan.tindak-lanjut-index', compact('rekomendasi', 'tindakLanjut'));
    }
} 