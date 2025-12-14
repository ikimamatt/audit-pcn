<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\Audit\PerencanaanAudit;
use App\Models\Audit\PelaporanTemuan;
use App\Models\MasterData\MasterUser;

class PelaporanHasilAuditController extends Controller
{
    public function index(Request $request)
    {
        // Get all data first - semua user bisa melihat semua data
        $query = PelaporanHasilAudit::with(['perencanaanAudit.auditee', 'temuan.kodeAoi', 'temuan.kodeRisk']);
        
        // Apply filters
        if ($request->filled('jenis_lha_lhk')) {
            $query->where('jenis_lha_lhk', $request->jenis_lha_lhk);
        }
        
        if ($request->filled('kode_spi')) {
            $query->where('kode_spi', $request->kode_spi);
        }
        
        if ($request->filled('status_approval')) {
            $query->where('status_approval', $request->status_approval);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        
        // Semua surat tugas bisa dilihat semua user
        $suratTugas = \App\Models\Audit\PerencanaanAudit::all();
        
        $kodeAoi = \App\Models\MasterData\MasterKodeAoi::all();
        $kodeRisk = \App\Models\MasterData\MasterKodeRisk::all();
        $selectedAudit = null;
        $temuanList = collect();
        if ($request->has('audit_id')) {
            $selectedAudit = PelaporanHasilAudit::with(['temuan.kodeAoi', 'temuan.kodeRisk'])->find($request->audit_id);
            $temuanList = $selectedAudit ? $selectedAudit->temuan : collect();
        }
        return view('audit.pelaporan.index', compact('data', 'suratTugas', 'kodeAoi', 'kodeRisk', 'selectedAudit', 'temuanList'));
    }

    public function create()
    {
        $suratTugas = \App\Models\Audit\PerencanaanAudit::all();
        $kodeAoi = \App\Models\MasterData\MasterKodeAoi::all();
        $kodeRisk = \App\Models\MasterData\MasterKodeRisk::all();
        $jenisAudit = \App\Models\MasterData\MasterJenisAudit::all();
        
        // Tidak generate nomor LHA/LHK otomatis di awal
        // Nomor akan di-generate ketika semua field yang diperlukan sudah terisi
        $nomorLhaLhk = '';
        
        return view('audit.pelaporan.create', compact('suratTugas', 'kodeAoi', 'kodeRisk', 'nomorLhaLhk', 'jenisAudit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'nomor_lha_lhk' => 'required|string',
            'jenis_lha_lhk' => 'required|in:LHA,LHK',
            'kode_spi' => 'required|string',
            'hasil_temuan' => 'required|array',
            'hasil_temuan.*' => 'required|string',
            'kode_aoi_id' => 'required|array',
            'kode_aoi_id.*' => 'required|exists:master_kode_aoi,id',
            'kode_risk_id' => 'required|array',
            'kode_risk_id.*' => 'required|exists:master_kode_risk,id',
            'nomor_iss' => 'required|array',
            'nomor_iss.*' => 'required|string',
            'nomor_urut_iss' => 'required|array',
            'nomor_urut_iss.*' => 'required|integer',
            'permasalahan' => 'required|array',
            'permasalahan.*' => 'required|string',
            'penyebab' => 'required|array',
            'penyebab.*' => 'required|string',
            'kriteria' => 'required|array',
            'kriteria.*' => 'required|string',
            'dampak_terjadi' => 'nullable|array',
            'dampak_terjadi.*' => 'nullable|string',
            'dampak_potensi' => 'nullable|array',
            'dampak_potensi.*' => 'nullable|string',
            'signifikan' => 'required|array',
            'signifikan.*' => 'required|in:Tinggi,Medium,Rendah',
        ]);

        // Extract nomor urut dari nomor LHA/LHK (format: xxx/AA/BB/CC/SPI.PCN.yyyy)
        $nomorParts = explode('/', $request->nomor_lha_lhk);
        $nomorUrut = intval($nomorParts[0]);
        
        // Create main pelaporan record
        $pelaporan = PelaporanHasilAudit::create([
            'perencanaan_audit_id' => $request->perencanaan_audit_id,
            'nomor_lha_lhk' => $request->nomor_lha_lhk,
            'jenis_lha_lhk' => $request->jenis_lha_lhk,
            'kode_spi' => $request->kode_spi,
            'jenis_audit_id' => $request->jenis_audit_id,
            'nomor_urut' => $nomorUrut,
            'tahun' => date('Y'),
            'status_approval' => 'pending',
        ]);

        // Create temuan records with comprehensive data
        foreach ($request->hasil_temuan as $index => $hasilTemuan) {
            \App\Models\Audit\PelaporanTemuan::create([
                'pelaporan_hasil_audit_id' => $pelaporan->id,
                'hasil_temuan' => $hasilTemuan,
                'kode_aoi_id' => $request->kode_aoi_id[$index],
                'kode_risk_id' => $request->kode_risk_id[$index],
                'nomor_iss' => $request->nomor_iss[$index],
                'nomor_urut_iss' => $request->nomor_urut_iss[$index],
                'tahun' => date('Y'),
                'permasalahan' => $request->permasalahan[$index],
                'penyebab' => $request->penyebab[$index],
                'kriteria' => $request->kriteria[$index],
                'dampak_terjadi' => $request->dampak_terjadi[$index] ?? null,
                'dampak_potensi' => $request->dampak_potensi[$index] ?? null,
                'signifikan' => $request->signifikan[$index],
                'status_approval' => 'pending',
            ]);
        }

        return redirect()->route('audit.pelaporan-hasil-audit.index')->with('success', 'Data pelaporan hasil audit berhasil disimpan!');
    }

    public function storeTemuan(Request $request)
    {
        $request->validate([
            'pelaporan_hasil_audit_id' => 'required|exists:pelaporan_hasil_audit,id',
            'hasil_temuan' => 'required|string',
            'kode_aoi_id' => 'required|exists:master_kode_aoi,id',
            'kode_risk_id' => 'required|exists:master_kode_risk,id',
            'nomor_iss' => 'required|string',
            'tahun' => 'required|digits:4',
            'permasalahan' => 'required|string',
            'penyebab' => 'required|string',
            'kriteria' => 'required|string',
            'dampak_terjadi' => 'nullable|string',
            'dampak_potensi' => 'nullable|string',
            'signifikan' => 'required|in:Tinggi,Medium,Rendah',
        ]);
        
        \App\Models\Audit\PelaporanTemuan::create($request->all());
        return redirect()->route('audit.pelaporan-hasil-audit.index', ['audit_id' => $request->pelaporan_hasil_audit_id])->with('success', 'Data temuan audit berhasil disimpan!');
    }

    public function show($id)
    {
        $item = PelaporanHasilAudit::with(['temuan.kodeAoi', 'temuan.kodeRisk'])->findOrFail($id);
        return view('audit.pelaporan.show', compact('item'));
    }

    public function edit($id)
    {
        $item = PelaporanHasilAudit::findOrFail($id);
        $suratTugas = \App\Models\Audit\PerencanaanAudit::all();
        $kodeAoi = \App\Models\MasterData\MasterKodeAoi::all();
        $kodeRisk = \App\Models\MasterData\MasterKodeRisk::all();
        return view('audit.pelaporan.edit', compact('item', 'suratTugas', 'kodeAoi', 'kodeRisk'));
    }

    public function editTemuan($id)
    {
        $temuan = \App\Models\Audit\PelaporanTemuan::findOrFail($id);
        $kodeAoi = \App\Models\MasterData\MasterKodeAoi::all();
        $kodeRisk = \App\Models\MasterData\MasterKodeRisk::all();
        return response()->json(['temuan' => $temuan, 'kodeAoi' => $kodeAoi, 'kodeRisk' => $kodeRisk]);
    }

    public function update(Request $request, $id)
    {
        $item = PelaporanHasilAudit::findOrFail($id);
        $request->validate([
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'nomor_lha_lhk' => 'required|string',
            'jenis_lha_lhk' => 'required|in:LHA,LHK',
            'jenis_audit_id' => 'required|exists:master_jenis_audit,id',
            'kode_spi' => 'required|in:SPI.01.02,SPI.01.03,SPI.01.04',
        ]);
        
        $data = $request->except('tahun');
        $data['jenis_audit_id'] = $request->jenis_audit_id;
        
        $item->update($data);
        return redirect()->route('audit.pelaporan-hasil-audit.index')->with('success', 'Data pelaporan hasil audit berhasil diupdate!');
    }

    public function updateTemuan(Request $request, $id)
    {
        try {
            // Log request data for debugging
            \Log::info('UpdateTemuan Request Data:', $request->all());
            
            $request->validate([
                'hasil_temuan' => 'required|string',
                'permasalahan' => 'required|string',
                'penyebab' => 'required|string',
                'kode_aoi_id' => 'required|exists:master_kode_aoi,id',
                'kode_risk_id' => 'required|exists:master_kode_risk,id',
                'kriteria' => 'required|string',
                'dampak_terjadi' => 'nullable|string',
                'dampak_potensi' => 'nullable|string',
                'signifikan' => 'required|in:Tinggi,Medium,Rendah',
            ]);

            $temuan = \App\Models\Audit\PelaporanTemuan::findOrFail($id);
            
            $temuan->update([
                'hasil_temuan' => $request->hasil_temuan,
                'permasalahan' => $request->permasalahan,
                'penyebab' => $request->penyebab,
                'kode_aoi_id' => $request->kode_aoi_id,
                'kode_risk_id' => $request->kode_risk_id,
                'kriteria' => $request->kriteria,
                'dampak_terjadi' => $request->dampak_terjadi,
                'dampak_potensi' => $request->dampak_potensi,
                'signifikan' => $request->signifikan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data ISS berhasil diperbarui',
                'data' => $temuan->fresh(['kodeAoi', 'kodeRisk'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data ISS: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $item = PelaporanHasilAudit::findOrFail($id);
        $item->delete();
        return redirect()->route('audit.pelaporan-hasil-audit.index')->with('success', 'Data pelaporan hasil audit berhasil dihapus!');
    }

    public function destroyTemuan($id)
    {
        $temuan = \App\Models\Audit\PelaporanTemuan::findOrFail($id);
        $audit_id = $temuan->pelaporan_hasil_audit_id;
        $temuan->delete();
        return redirect()->route('audit.pelaporan-hasil-audit.index', ['audit_id' => $audit_id])->with('success', 'Data temuan audit berhasil dihapus!');
    }

    public function approval($id, Request $request)
    {
        $item = PelaporanHasilAudit::with(['temuan'])->findOrFail($id);
        
        // Validasi alasan penolakan jika reject
        if ($request->action == 'reject') {
            $request->validate([
                'rejection_reason' => 'required|string|min:10',
            ], [
                'rejection_reason.required' => 'Alasan penolakan harus diisi',
                'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter',
            ]);
        }

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $item,
            $request->action,
            $request->rejection_reason ?? $request->alasan_reject ?? null
        );

        if ($result['success']) {
            // Jika approve final (level 2), approve semua ISS yang terkait
            if ($request->action == 'approve' && $item->status_approval === 'approved') {
                if ($item->temuan && $item->temuan->count() > 0) {
                    foreach ($item->temuan as $temuan) {
                        $temuan->update([
                            'status_approval' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now()
                        ]);
                    }
                    return redirect()->back()->with('success', $result['message'] . ' Semua ISS juga berhasil diapprove!');
                }
            }
            
            // Jika reject final (level 2), reject semua ISS yang terkait
            if ($request->action == 'reject' && $item->status_approval === 'rejected') {
                if ($item->temuan && $item->temuan->count() > 0) {
                    foreach ($item->temuan as $temuan) {
                        $temuan->update([
                            'status_approval' => 'rejected',
                            'approved_by' => auth()->id(),
                            'approved_at' => now()
                        ]);
                    }
                    return redirect()->back()->with('success', $result['message'] . ' Semua ISS juga berhasil direject!');
                }
            }
            
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function generateNomorLhk(Request $request)
    {
        $request->validate([
            'jenis_lha_lhk' => 'required|in:LHA,LHK',
            'jenis_audit_id' => 'required|exists:master_jenis_audit,id',
            'kode_spi' => 'required|string',
        ]);

        // Get jenis audit untuk menentukan PO/Konsul
        $jenisAudit = \App\Models\MasterData\MasterJenisAudit::findOrFail($request->jenis_audit_id);
        
        // Tentukan PO/Konsul berdasarkan jenis audit
        // Audit Operasional (SPI.01.02) dan Audit Khusus (SPI.01.03) = PO AUDIT
        // Konsultasi (SPI.01.04) = KONSUL
        $poKonsul = ($jenisAudit->kode == 'SPI.01.04') ? 'KONSUL' : 'POAUDIT';

        $currentYear = date('Y');
        $lastLhaLhk = PelaporanHasilAudit::where('tahun', $currentYear)
            ->orderBy('nomor_urut', 'desc')
            ->first();
        
        $nextNumber = $lastLhaLhk ? ($lastLhaLhk->nomor_urut + 1) : 1;
        
        // Format: xxx/AA/BB/CC/SPI.PCN.yyyy
        // AA = LHA atau LHK
        // BB = PO AUDIT atau KONSUL (tanpa spasi)
        // CC = SPI.01.02 atau SPI.01.03 atau SPI.01.04
        $jenis = $request->jenis_lha_lhk;
        $kodeSpi = $request->kode_spi;
        
        $nomorLhaLhk = sprintf('%03d', $nextNumber) . '/' . $jenis . '/' . $poKonsul . '/' . $kodeSpi . '/SPI.PCN.' . $currentYear;
        
        return response()->json([
            'nomor_lha_lhk' => $nomorLhaLhk,
            'nomor_urut' => $nextNumber
        ]);
    }

    public function generateNomorIss(Request $request)
    {
        $request->validate([
            'nomor_lha_lhk' => 'required|string',
            'kode_spi' => 'required|in:SPI.01.02,SPI.01.03,SPI.01.04',
            'kode_aoi_id' => 'required|exists:master_kode_aoi,id',
            'kode_risk_id' => 'required|exists:master_kode_risk,id',
        ]);

        $nomorLhaLhk = $request->nomor_lha_lhk;
        $kodeAoi = \App\Models\MasterData\MasterKodeAoi::find($request->kode_aoi_id);
        $kodeRisk = \App\Models\MasterData\MasterKodeRisk::find($request->kode_risk_id);
        
        // Format: ISS.xxx/PO PCN/MM/NN/PP/yyyy
        // xxx = nomor urut ISS (berbeda dari nomor LHA/LHK, dimulai dari 001)
        // PO PCN = tetap (tidak berubah)
        // MM = SPI.01.02 atau SPI.01.03 atau SPI.01.04 (dari kode SPI yang dipilih user)
        // NN = kode AOI
        // PP = kode risiko
        // yyyy = tahun
        
        // Ambil kode SPI dari request (yang dipilih user)
        $kodeSpi = $request->kode_spi ?? 'SPI.01.02'; // Default jika tidak ada
        
        // Generate nomor urut ISS yang berbeda dari nomor LHA/LHK
        $currentYear = date('Y');
        $lastIss = \App\Models\Audit\PelaporanTemuan::where('tahun', $currentYear)
            ->orderBy('nomor_urut_iss', 'desc')
            ->first();
        
        $nextIssNumber = $lastIss ? ($lastIss->nomor_urut_iss + 1) : 1;
        
        $nomorIss = 'ISS.' . sprintf('%03d', $nextIssNumber) . '/PO PCN/' . $kodeSpi . '/' . $kodeAoi->kode_area_of_improvement . '/' . $kodeRisk->kode_risiko . '/' . $currentYear;
        
        return response()->json([
            'nomor_iss' => $nomorIss,
            'nomor_urut_iss' => $nextIssNumber
        ]);
    }

    public function getTemuanData($id)
    {
        try {
            $pelaporan = PelaporanHasilAudit::with([
                'temuan.kodeAoi', 
                'temuan.kodeRisk'
            ])->findOrFail($id);
            
            $temuanData = $pelaporan->temuan->map(function($temuan) {
                return [
                    'id' => $temuan->id,
                    'nomor_urut_iss' => $temuan->nomor_urut_iss,
                    'nomor_iss' => $temuan->nomor_iss,
                    'hasil_temuan' => $temuan->hasil_temuan,
                    'permasalahan' => $temuan->permasalahan,
                    'penyebab' => $temuan->penyebab,
                    'kriteria' => $temuan->kriteria,
                    'dampak_terjadi' => $temuan->dampak_terjadi,
                    'dampak_potensi' => $temuan->dampak_potensi,
                    'signifikan' => $temuan->signifikan,
                    'status_approval' => $temuan->status_approval,
                    'kode_aoi_id' => $temuan->kode_aoi_id,
                    'kode_risk_id' => $temuan->kode_risk_id,
                    'kode_aoi' => [
                        'kode_area_of_improvement' => $temuan->kodeAoi->kode_area_of_improvement ?? null,
                        'deskripsi_area_of_improvement' => $temuan->kodeAoi->deskripsi_area_of_improvement ?? null,
                    ],
                    'kode_risk' => [
                        'kode_risiko' => $temuan->kodeRisk->kode_risiko ?? null,
                        'deskripsi_risiko' => $temuan->kodeRisk->deskripsi_risiko ?? null,
                    ],
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $temuanData,
                'pelaporan' => [
                    'id' => $pelaporan->id,
                    'nomor_lha_lhk' => $pelaporan->nomor_lha_lhk,
                    'jenis_lha_lhk' => $pelaporan->jenis_lha_lhk,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data temuan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTemuanById($id)
    {
        try {
            $temuan = \App\Models\Audit\PelaporanTemuan::with(['kodeAoi', 'kodeRisk'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $temuan->id,
                    'hasil_temuan' => $temuan->hasil_temuan,
                    'permasalahan' => $temuan->permasalahan,
                    'penyebab' => $temuan->penyebab,
                    'kode_aoi_id' => $temuan->kode_aoi_id,
                    'kode_risk_id' => $temuan->kode_risk_id,
                    'kriteria' => $temuan->kriteria,
                    'dampak_terjadi' => $temuan->dampak_terjadi,
                    'dampak_potensi' => $temuan->dampak_potensi,
                    'signifikan' => $temuan->signifikan,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data temuan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAllTemuanForPenutup()
    {
        try {
            // Log untuk debugging
            \Log::info('getAllTemuanForPenutup called');
            
            // Get all temuan first to see what's available
            $allTemuan = \App\Models\Audit\PelaporanTemuan::all();
            \Log::info('Total temuan found:', ['count' => $allTemuan->count()]);
            
            // Check status_approval values
            $statusCounts = $allTemuan->groupBy('status_approval')->map->count();
            \Log::info('Status approval counts:', $statusCounts->toArray());
            
            // Temporarily remove status filter to see all data
            $temuanList = \App\Models\Audit\PelaporanTemuan::with(['pelaporanHasilAudit'])
                ->get();
            
            \Log::info('Approved temuan found:', ['count' => $temuanList->count()]);
            
            $mappedData = $temuanList->map(function($temuan) {
                return [
                    'id' => $temuan->id,
                    'nomor_iss' => $temuan->nomor_iss,
                    'hasil_temuan' => $temuan->hasil_temuan,
                    'permasalahan' => $temuan->permasalahan,
                    'pelaporan_hasil_audit' => [
                        'id' => $temuan->pelaporanHasilAudit->id,
                        'nomor_lha_lhk' => $temuan->pelaporanHasilAudit->nomor_lha_lhk,
                        'jenis_lha_lhk' => $temuan->pelaporanHasilAudit->jenis_lha_lhk,
                    ]
                ];
            });
            
            \Log::info('Mapped data:', ['count' => $mappedData->count()]);
            
            return response()->json([
                'success' => true,
                'data' => $mappedData
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getAllTemuanForPenutup:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data temuan: ' . $e->getMessage()
            ], 500);
        }
    }
}
