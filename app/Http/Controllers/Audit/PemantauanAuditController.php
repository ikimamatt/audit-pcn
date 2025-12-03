<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\PenutupLhaRekomendasi;
use App\Models\Audit\PerencanaanAudit;
use Illuminate\Http\Request;

class PemantauanAuditController extends Controller
{
    public function selectNomorSuratTugas(Request $request)
    {
        // Ambil semua perencanaan audit yang memiliki rekomendasi
        $query = PerencanaanAudit::whereHas('pelaporanHasilAudit.temuan.penutupLhaRekomendasi');
        
        // Filter berdasarkan search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat_tugas', 'like', '%' . $search . '%')
                  ->orWhereHas('pelaporanHasilAudit', function($q2) use ($search) {
                      $q2->where('nomor_lha_lhk', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Filter berdasarkan jenis audit
        if ($request->filled('jenis_audit')) {
            $query->where('jenis_audit', $request->jenis_audit);
        }
        
        $nomorSuratTugasList = $query->with(['pelaporanHasilAudit.temuan.penutupLhaRekomendasi'])
            ->get()
            ->map(function($perencanaan) {
                $totalRekomendasi = 0;
                $nomorLhaLhkList = [];
                
                foreach ($perencanaan->pelaporanHasilAudit as $lha) {
                    foreach ($lha->temuan as $temuan) {
                        if ($temuan->penutupLhaRekomendasi) {
                            $totalRekomendasi++;
                        }
                    }
                    if ($lha->nomor_lha_lhk) {
                        $nomorLhaLhkList[] = $lha->nomor_lha_lhk;
                    }
                }
                
                return [
                    'nomor_surat_tugas' => $perencanaan->nomor_surat_tugas,
                    'perencanaan_audit_id' => $perencanaan->id,
                    'jenis_audit' => $perencanaan->jenis_audit,
                    'nomor_lha_lhk' => implode(', ', array_unique($nomorLhaLhkList)),
                    'count_rekomendasi' => $totalRekomendasi,
                ];
            })
            ->where('count_rekomendasi', '>', 0) // Hanya yang punya rekomendasi
            ->sortBy('nomor_surat_tugas')
            ->values();
        
        // Ambil daftar jenis audit untuk filter dropdown
        $jenisAuditList = PerencanaanAudit::whereHas('pelaporanHasilAudit.temuan.penutupLhaRekomendasi')
            ->distinct()
            ->pluck('jenis_audit')
            ->sort()
            ->values();
        
        return view('audit.pemantauan.select-nomor-surat-tugas', compact('nomorSuratTugasList', 'jenisAuditList'));
    }

    public function index(Request $request)
    {
        // Jika tidak ada nomor_surat_tugas, redirect ke halaman pemilihan
        if (!$request->filled('nomor_surat_tugas')) {
            return redirect()->route('audit.pemantauan.select-nomor-surat-tugas');
        }
        
        $nomorSuratTugas = $request->get('nomor_surat_tugas');
        
        // Load data with all necessary relationships
        $query = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'tindakLanjut'
        ]);
        
        // Filter berdasarkan nomor surat tugas
        if ($nomorSuratTugas) {
            $query->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($nomorSuratTugas) {
                $q->where('nomor_surat_tugas', $nomorSuratTugas);
            });
        }
        
        // Apply month filter if provided
        if ($request->filled('bulan')) {
            [$year, $month] = explode('-', $request->bulan);
            $query->whereYear('target_waktu', $year)->whereMonth('target_waktu', $month);
        }
        
        $data = $query->get();
        
        // Ambil info perencanaan audit untuk ditampilkan
        $perencanaanAudit = null;
        if ($nomorSuratTugas) {
            $perencanaanAudit = PerencanaanAudit::where('nomor_surat_tugas', $nomorSuratTugas)->first();
        }
        
        return view('audit.pemantauan.index', compact('data', 'nomorSuratTugas', 'perencanaanAudit'));
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
        
        // Ambil nomor surat tugas dari rekomendasi
        $nomorSuratTugas = null;
        if ($item->temuan && $item->temuan->pelaporanHasilAudit && $item->temuan->pelaporanHasilAudit->perencanaanAudit) {
            $nomorSuratTugas = $item->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas;
        }
        
        return redirect()->route('audit.pemantauan.index', ['nomor_surat_tugas' => $nomorSuratTugas])->with('success', 'Rekomendasi berhasil diupdate!');
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