<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\PenutupLhaRekomendasi;
use App\Models\Audit\PerencanaanAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AuthHelper;

class PemantauanAuditController extends Controller
{
    public function selectNomorSuratTugas(Request $request)
    {
        $currentUserId = AuthHelper::getCurrentUserId();
        $canSeeAllData = AuthHelper::canSeeAllData();
        $isPic = AuthHelper::isPic();
        
        // Ambil semua perencanaan audit yang memiliki rekomendasi
        $query = PerencanaanAudit::whereHas('pelaporanHasilAudit.temuan.penutupLhaRekomendasi');
        
        // Jika user adalah PIC (bukan admin), filter hanya rekomendasi dimana user tersebut adalah PIC
        if ($isPic && !$canSeeAllData && $currentUserId) {
            $query->whereHas('pelaporanHasilAudit.temuan.penutupLhaRekomendasi.picUsers', function($q) use ($currentUserId) {
                $q->where('master_user_id', $currentUserId);
            });
        }
        
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
            ->map(function($perencanaan) use ($currentUserId, $canSeeAllData, $isPic) {
                $totalRekomendasi = 0;
                $nomorLhaLhkList = [];
                
                foreach ($perencanaan->pelaporanHasilAudit as $lha) {
                    foreach ($lha->temuan as $temuan) {
                        if ($temuan->penutupLhaRekomendasi) {
                            // Jika user adalah PIC, hanya hitung rekomendasi dimana user tersebut adalah PIC
                            if ($isPic && !$canSeeAllData && $currentUserId) {
                                $isUserPic = $temuan->penutupLhaRekomendasi->picUsers()
                                    ->where('master_user_id', $currentUserId)
                                    ->exists();
                                if ($isUserPic) {
                                    $totalRekomendasi++;
                                }
                            } else {
                                $totalRekomendasi++;
                            }
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
        
        // Ambil daftar jenis audit untuk filter dropdown (dengan filter PIC jika perlu)
        $jenisAuditQuery = PerencanaanAudit::whereHas('pelaporanHasilAudit.temuan.penutupLhaRekomendasi');
        
        if ($isPic && !$canSeeAllData && $currentUserId) {
            $jenisAuditQuery->whereHas('pelaporanHasilAudit.temuan.penutupLhaRekomendasi.picUsers', function($q) use ($currentUserId) {
                $q->where('master_user_id', $currentUserId);
            });
        }
        
        $jenisAuditList = $jenisAuditQuery
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
        $currentUserId = AuthHelper::getCurrentUserId();
        $canSeeAllData = AuthHelper::canSeeAllData();
        $isPic = AuthHelper::isPic();
        
        // Load data with all necessary relationships
        $query = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'tindakLanjut',
            'picUsers'
        ]);
        
        // Filter berdasarkan nomor surat tugas
        if ($nomorSuratTugas) {
            $query->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($nomorSuratTugas) {
                $q->where('nomor_surat_tugas', $nomorSuratTugas);
            });
        }
        
        // Jika user adalah PIC (bukan admin), filter hanya rekomendasi dimana user tersebut adalah PIC
        if ($isPic && !$canSeeAllData && $currentUserId) {
            $query->whereHas('picUsers', function($q) use ($currentUserId) {
                $q->where('master_user_id', $currentUserId);
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
        $currentUserId = AuthHelper::getCurrentUserId();
        $canSeeAllData = AuthHelper::canSeeAllData();
        $isPic = AuthHelper::isPic();
        
        $item = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'picUsers'
        ])->findOrFail($id);
        
        // Jika user adalah PIC (bukan admin), pastikan user tersebut adalah PIC dari rekomendasi ini
        if ($isPic && !$canSeeAllData && $currentUserId) {
            $isUserPic = $item->picUsers()->where('master_user_id', $currentUserId)->exists();
            if (!$isUserPic) {
                abort(403, 'Anda tidak memiliki akses untuk mengedit rekomendasi ini.');
            }
        }
        
        return view('audit.pemantauan.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $isPic = false;
        
        // Cek apakah user adalah PIC (bukan KSPI, ASMAN SPI, atau AUDITOR)
        if ($user && $user->akses) {
            $namaAkses = $user->akses->nama_akses;
            $isPic = !in_array($namaAkses, ['KSPI', 'ASMAN SPI', 'Auditor', 'AUDITOR']);
        }
        
        $item = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'picUsers'
        ])->findOrFail($id);
        
        // Jika user adalah PIC, pastikan user tersebut adalah PIC dari rekomendasi ini
        if ($isPic && $user) {
            $isRelated = $item->picUsers()->where('master_user_id', $user->id)->exists();
            if (!$isRelated) {
                abort(403, 'Anda tidak memiliki akses untuk mengupdate rekomendasi ini.');
            }
        }
        
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