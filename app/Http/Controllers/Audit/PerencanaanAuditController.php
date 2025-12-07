<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\Audit\PerencanaanAudit;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterJenisAudit;
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
        $jenisAudits = MasterJenisAudit::all();
        // Ambil user dengan role "Auditor" dan "PIC Auditor" (atau "PIC Auditee" jika "PIC Auditor" tidak ada)
        $auditors = MasterUser::with('akses')->whereHas('akses', function($q) {
            $q->whereIn('nama_akses', ['Auditor', 'PIC Auditor', 'PIC Auditee']);
        })->orderBy('nama')->get();
        
        return view('audit.perencanaan.create', compact('auditees', 'auditors', 'jenisAudits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_surat_tugas' => 'required|date',
            'nomor_surat_tugas' => 'required|string|max:255',
            'jenis_audit_id' => 'required|exists:master_jenis_audit,id',
            'auditor' => 'nullable|array',
            'auditor.*' => 'nullable|exists:master_user,id',
            'auditee' => 'required|exists:master_auditee,id',
            'ruang_lingkup' => 'required|array',
            'tanggal_audit_mulai' => 'required|date',
            'tanggal_audit_sampai' => 'required|date',
            'periode_awal' => 'required',
            'periode_akhir' => 'required',
        ]);
        
        // Gunakan nomor surat tugas dari input manual
        $nomorSuratTugas = $request->nomor_surat_tugas;
        
        // Konversi ID auditor menjadi format nama + NIP
        $auditorData = [];
        if ($request->auditor && is_array($request->auditor)) {
            foreach ($request->auditor as $auditorId) {
                if (!empty($auditorId) && is_numeric($auditorId)) {
                    $auditor = MasterUser::find($auditorId);
                    if ($auditor) {
                        $auditorData[] = $auditor->nama . ' - NIP: ' . $auditor->nip;
                    }
                }
            }
        }
        
        // Ambil nama jenis audit dari master data
        $jenisAudit = MasterJenisAudit::find($request->jenis_audit_id);
        
        $perencanaan = PerencanaanAudit::create([
            'tanggal_surat_tugas' => $request->tanggal_surat_tugas,
            'nomor_surat_tugas' => $nomorSuratTugas,
            'jenis_audit_id' => $request->jenis_audit_id,
            'jenis_audit' => $jenisAudit ? $jenisAudit->nama_jenis_audit : null, // Simpan juga untuk backward compatibility
            'auditor' => $auditorData,
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
        // Ambil user dengan role "Auditor" dan "PIC Auditor" (atau "PIC Auditee" jika "PIC Auditor" tidak ada)
        $auditors = MasterUser::with('akses')->whereHas('akses', function($q) {
            $q->whereIn('nama_akses', ['Auditor', 'PIC Auditor', 'PIC Auditee']);
        })->orderBy('nama')->get();
        
        // Mencocokkan auditor lama dengan user baru berdasarkan NIP
        $matchedAuditorIds = [];
        if ($item->auditor && is_array($item->auditor)) {
            foreach ($item->auditor as $auditorText) {
                // Parse format: "Nama - NIP: xxxxx" atau format lain
                if (preg_match('/NIP:\s*([^\s-]+)/', $auditorText, $matches)) {
                    $nip = trim($matches[1]);
                    $matchedUser = MasterUser::where('nip', $nip)->first();
                    if ($matchedUser) {
                        $matchedAuditorIds[] = $matchedUser->id;
                    }
                }
            }
        }
        $item->matched_auditor_ids = $matchedAuditorIds;
        
        return view('audit.perencanaan.edit', compact('item', 'auditees', 'auditors'));
    }

    public function update(Request $request, $id)
    {
        $item = PerencanaanAudit::findOrFail($id);
        
        $request->validate([
            'tanggal_surat_tugas' => 'required|date',
            'nomor_surat_tugas' => 'required|string|max:255',
            'jenis_audit_id' => 'required|exists:master_jenis_audit,id',
            'auditor' => 'nullable|array',
            'auditor.*' => 'nullable|exists:master_user,id',
            'auditee' => 'required|exists:master_auditee,id',
            'ruang_lingkup' => 'required|array',
            'tanggal_audit_mulai' => 'required|date',
            'tanggal_audit_sampai' => 'required|date',
            'periode_awal' => 'required',
            'periode_akhir' => 'required',
        ]);
        
        // Gunakan nomor surat tugas dari input manual
        $nomorSuratTugas = $request->nomor_surat_tugas;
        
        // Konversi ID auditor menjadi format nama + NIP
        $auditorData = [];
        if ($request->auditor && is_array($request->auditor)) {
            foreach ($request->auditor as $auditorId) {
                if (!empty($auditorId) && is_numeric($auditorId)) {
                    $auditor = MasterUser::find($auditorId);
                    if ($auditor) {
                        $auditorData[] = $auditor->nama . ' - NIP: ' . $auditor->nip;
                    }
                }
            }
        }
        
        // Ambil nama jenis audit dari master data
        $jenisAudit = MasterJenisAudit::find($request->jenis_audit_id);
        
        $item->update([
            'tanggal_surat_tugas' => $request->tanggal_surat_tugas,
            'nomor_surat_tugas' => $nomorSuratTugas,
            'jenis_audit_id' => $request->jenis_audit_id,
            'jenis_audit' => $jenisAudit ? $jenisAudit->nama_jenis_audit : null, // Simpan juga untuk backward compatibility
            'auditor' => $auditorData,
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