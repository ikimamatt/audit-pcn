<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\Audit\PerencanaanAudit;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterUser;
use Illuminate\Http\Request;

class PerencanaanAuditController extends Controller
{
    public function index()
    {
        $data = PerencanaanAudit::with('auditee')->get();
        return view('audit.perencanaan.index', compact('data'));
    }

    public function create()
    {
        $auditees = MasterAuditee::all();
        $auditors = MasterUser::with('akses')->whereHas('akses', function($q) {
            $q->where('nama_akses', 'Auditor');
        })->get();
        
        // Generate nomor surat tugas otomatis
        $nomorSuratTugas = $this->generateNomorSuratTugas();
        
        return view('audit.perencanaan.create', compact('auditees', 'auditors', 'nomorSuratTugas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_surat_tugas' => 'required|date',
            'jenis_audit' => 'required',
            'auditor' => 'nullable|array',
            'auditee' => 'required|exists:master_auditee,id',
            'ruang_lingkup' => 'required|array',
            'tanggal_audit_mulai' => 'required|date',
            'tanggal_audit_sampai' => 'required|date',
            'periode_awal' => 'required',
            'periode_akhir' => 'required',
        ]);
        
        // Generate nomor surat tugas otomatis berdasarkan jenis audit
        $nomorSuratTugas = $this->generateNomorSuratTugas($request->jenis_audit);
        
        $perencanaan = PerencanaanAudit::create([
            'tanggal_surat_tugas' => $request->tanggal_surat_tugas,
            'nomor_surat_tugas' => $nomorSuratTugas,
            'jenis_audit' => $request->jenis_audit,
            'auditor' => $request->auditor ?? [],
            'auditee_id' => $request->auditee,
            'ruang_lingkup' => $request->ruang_lingkup,
            'tanggal_audit_mulai' => $request->tanggal_audit_mulai,
            'tanggal_audit_sampai' => $request->tanggal_audit_sampai,
            'periode_audit' => $request->periode_awal . ' s/d ' . $request->periode_akhir,
        ]);
        
        // Redirect ke index dengan session data untuk modal
        return redirect()->route('audit.perencanaan.index')->with([
            'success' => 'Data perencanaan audit berhasil disimpan!',
            'nomor' => $perencanaan->nomor_surat_tugas
        ]);
    }

    public function edit($id)
    {
        $item = PerencanaanAudit::findOrFail($id);
        
        // Memisahkan periode_audit menjadi periode_awal dan periode_akhir
        if ($item->periode_audit) {
            $periodeParts = explode(' s/d ', $item->periode_audit);
            $item->periode_awal = $periodeParts[0] ?? '';
            $item->periode_akhir = $periodeParts[1] ?? '';
        }
        
        $auditees = MasterAuditee::all();
        $auditors = MasterUser::with('akses')->whereHas('akses', function($q) {
            $q->where('nama_akses', 'Auditor');
        })->get();
        return view('audit.perencanaan.edit', compact('item', 'auditees', 'auditors'));
    }

    public function update(Request $request, $id)
    {
        $item = PerencanaanAudit::findOrFail($id);
        
        // Generate nomor surat tugas otomatis jika jenis audit berubah
        $nomorSuratTugas = $item->jenis_audit !== $request->jenis_audit 
            ? $this->generateNomorSuratTugas($request->jenis_audit)
            : $item->nomor_surat_tugas;
        
        $item->update([
            'tanggal_surat_tugas' => $request->tanggal_surat_tugas,
            'nomor_surat_tugas' => $nomorSuratTugas,
            'jenis_audit' => $request->jenis_audit,
            'auditor' => $request->auditor ?? [],
            'auditee_id' => $request->auditee,
            'ruang_lingkup' => $request->ruang_lingkup,
            'tanggal_audit_mulai' => $request->tanggal_audit_mulai,
            'tanggal_audit_sampai' => $request->tanggal_audit_sampai,
            'periode_audit' => $request->periode_awal . ' s/d ' . $request->periode_akhir,
        ]);
        
        // Redirect ke index dengan session data untuk modal
        return redirect()->route('audit.perencanaan.index')->with([
            'success' => 'Data perencanaan audit berhasil diupdate!',
            'nomor' => $item->nomor_surat_tugas
        ]);
    }

    public function destroy($id)
    {
        $item = PerencanaanAudit::findOrFail($id);
        
        // Cek apakah ada data terkait yang mencegah penghapusan
        $relatedData = [];
        
        // Cek Program Kerja Audit
        if ($item->programKerjaAudit()->count() > 0) {
            $relatedData[] = 'Program Kerja Audit';
        }
        
        // Cek Pelaporan Hasil Audit
        if ($item->pelaporanHasilAudit()->count() > 0) {
            $relatedData[] = 'Pelaporan Hasil Audit';
        }
        
        // Cek Walkthrough Audit
        if ($item->walkthroughAudit()->count() > 0) {
            $relatedData[] = 'Walkthrough Audit';
        }
        
        // Jika ada data terkait, tampilkan pesan error
        if (!empty($relatedData)) {
            $relatedDataList = implode(', ', $relatedData);
            return redirect()->route('audit.perencanaan.index')->with('error', 
                "Tidak dapat menghapus data ini karena masih terkait dengan: {$relatedDataList}. " .
                "Silakan hapus data terkait terlebih dahulu."
            );
        }
        
        try {
            $item->delete();
            return redirect()->route('audit.perencanaan.index')->with('success', 'Data perencanaan audit berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('audit.perencanaan.index')->with('error', 
                'Terjadi kesalahan saat menghapus data. Silakan coba lagi atau hubungi administrator.'
            );
        }
    }

    /**
     * API endpoint untuk mendapatkan nomor surat tugas otomatis
     */
    public function getNomorSuratTugas(Request $request)
    {
        $jenisAudit = $request->input('jenis_audit');
        $nomorSuratTugas = $this->generateNomorSuratTugas($jenisAudit);
        
        return response()->json([
            'nomor_surat_tugas' => $nomorSuratTugas
        ]);
    }

    /**
     * Generate nomor surat tugas otomatis
     * Format: 001.STG/SPI.01.XX/SPI-PCN/2025
     * 
     * @param string|null $jenisAudit
     * @return string
     */
    private function generateNomorSuratTugas($jenisAudit = null)
    {
        $tahun = date('Y');
        $kodeJenis = '02'; // Default untuk audit operasional
        
        // Mapping jenis audit ke kode
        if ($jenisAudit) {
            switch (strtolower($jenisAudit)) {
                case 'audit operasional':
                    $kodeJenis = '02';
                    break;
                case 'audit khusus':
                    $kodeJenis = '03';
                    break;
                case 'konsultasi':
                    $kodeJenis = '04';
                    break;
                default:
                    $kodeJenis = '02';
                    break;
            }
        }
        
        // Hitung nomor urut berdasarkan jenis audit dan tahun
        $lastNomor = PerencanaanAudit::where('jenis_audit', $jenisAudit)
            ->whereYear('created_at', $tahun)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastNomor) {
            // Extract nomor urut dari nomor surat tugas yang sudah ada
            $nomorParts = explode('.', $lastNomor->nomor_surat_tugas);
            $nomorUrut = intval($nomorParts[0]) + 1;
        } else {
            $nomorUrut = 1;
        }
        
        // Format nomor dengan leading zeros
        $nomorFormatted = str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);
        
        return "{$nomorFormatted}.STG/SPI.01.{$kodeJenis}/SPI-PCN/{$tahun}";
    }
} 