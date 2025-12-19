<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\PenutupLhaRekomendasi;
use App\Models\PenutupLhaTindakLanjut;
// use App\Models\Models\Audit\PelaporanIsiLha;
use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\Audit\PelaporanTemuan;
use App\Models\Audit\PerencanaanAudit;
use App\Models\MasterData\MasterUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PenutupLhaRekomendasiController extends Controller
{
    public function selectNomorSuratTugas(Request $request)
    {
        // Ambil semua nomor surat tugas yang memiliki PelaporanHasilAudit dengan temuan yang sudah approved
        $query = PerencanaanAudit::with(['pelaporanHasilAudit.temuan'])
            ->whereHas('pelaporanHasilAudit.temuan', function($q) {
                $q->where('status_approval', 'approved');
            });
        
        // Filter berdasarkan jenis audit
        if ($request->filled('jenis_audit')) {
            $query->where('jenis_audit', $request->jenis_audit);
        }
        
        // Filter berdasarkan search (nomor surat tugas atau nomor LHA/LHK)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat_tugas', 'like', '%' . $search . '%')
                  ->orWhereHas('pelaporanHasilAudit', function($q2) use ($search) {
                      $q2->where('nomor_lha_lhk', 'like', '%' . $search . '%');
                  });
            });
        }
        
        $nomorSuratTugasList = $query->get()
            ->map(function($perencanaan) {
                $totalTemuan = 0;
                $nomorLhaLhkList = [];
                
                foreach ($perencanaan->pelaporanHasilAudit as $lha) {
                    $approvedTemuan = $lha->temuan->where('status_approval', 'approved');
                    $totalTemuan += $approvedTemuan->count();
                    if ($lha->nomor_lha_lhk) {
                        $nomorLhaLhkList[] = $lha->nomor_lha_lhk;
                    }
                }
                
                return [
                    'nomor_surat_tugas' => $perencanaan->nomor_surat_tugas,
                    'perencanaan_audit_id' => $perencanaan->id,
                    'jenis_audit' => $perencanaan->jenis_audit,
                    'nomor_lha_lhk' => implode(', ', array_unique($nomorLhaLhkList)),
                    'count_temuan' => $totalTemuan,
                ];
            })
            ->sortBy('nomor_surat_tugas')
            ->values();
        
        // Ambil daftar jenis audit untuk filter dropdown
        $jenisAuditList = PerencanaanAudit::whereHas('pelaporanHasilAudit.temuan', function($q) {
                $q->where('status_approval', 'approved');
            })
            ->distinct()
            ->pluck('jenis_audit')
            ->sort()
            ->values();
        
        return view('audit.pelaporan.penutup-lha.select-nomor-surat-tugas', compact('nomorSuratTugasList', 'jenisAuditList'));
    }

    public function index(Request $request)
    {
        // Jika tidak ada nomor_surat_tugas, redirect ke halaman pemilihan
        if (!$request->filled('nomor_surat_tugas')) {
            return redirect()->route('audit.penutup-lha-rekomendasi.select-nomor-surat-tugas');
        }
        
        $nomorSuratTugas = $request->get('nomor_surat_tugas');
        $isiLhaId = $request->get('pelaporan_isi_lha_id');
        
        // Get all data first with relationships
        $query = PenutupLhaRekomendasi::with(['approvedBy', 'temuan.pelaporanHasilAudit.perencanaanAudit', 'picUsers.auditee']);
        
        // Filter berdasarkan nomor surat tugas
        if ($nomorSuratTugas) {
            $query->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($nomorSuratTugas) {
                $q->where('nomor_surat_tugas', $nomorSuratTugas);
            });
        }
        
        // Apply filters
        if ($isiLhaId) {
            $query->where('pelaporan_isi_lha_id', $isiLhaId);
        }
        
        if ($request->filled('status_approval')) {
            $query->where('status_approval', $request->status_approval);
        }
        
        if ($request->filled('search')) {
            $query->where('rekomendasi', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('pic')) {
            $query->where('pic_rekomendasi', 'like', '%' . $request->pic . '%');
        }
        
        $data = $query->get();
        
        // Ambil info perencanaan audit untuk ditampilkan
        $perencanaanAudit = null;
        if ($nomorSuratTugas) {
            $perencanaanAudit = \App\Models\Audit\PerencanaanAudit::where('nomor_surat_tugas', $nomorSuratTugas)->first();
        }
        
        return view('audit.pelaporan.penutup-lha.index', compact('data', 'isiLhaId', 'nomorSuratTugas', 'perencanaanAudit'));
    }

    public function create(Request $request)
    {
        $isiLhaId = $request->get('pelaporan_isi_lha_id');
        $nomorSuratTugas = $request->get('nomor_surat_tugas');
        
        // Get approved ISS data from PelaporanTemuan
        $query = PelaporanTemuan::where('status_approval', 'approved')
            ->with(['pelaporanHasilAudit.perencanaanAudit']);
        
        // Filter berdasarkan nomor surat tugas jika ada
        if ($nomorSuratTugas) {
            $query->whereHas('pelaporanHasilAudit.perencanaanAudit', function($q) use ($nomorSuratTugas) {
                $q->where('nomor_surat_tugas', $nomorSuratTugas);
            });
        }
        
        $approvedIss = $query->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_iss' => $item->nomor_iss,
                    'nomor_lha_lhk' => $item->pelaporanHasilAudit->nomor_lha_lhk ?? '-',
                    'hasil_temuan' => $item->hasil_temuan,
                    'permasalahan' => $item->permasalahan
                ];
            });
        
        // Ambil semua user dari master_user untuk dipilih sebagai PIC
        $picUsers = MasterUser::with(['akses', 'auditee'])
            ->orderBy('nama')
            ->get();
        
        return view('audit.pelaporan.penutup-lha.create', compact('isiLhaId', 'approvedIss', 'picUsers', 'nomorSuratTugas'));
    }

    public function getIssData(Request $request)
    {
        $nomorSuratTugas = $request->get('nomor_surat_tugas');
        
        // Get approved ISS data for dropdown
        $query = PelaporanTemuan::where('status_approval', 'approved')
            ->with(['pelaporanHasilAudit.perencanaanAudit']);
        
        // Filter berdasarkan nomor surat tugas jika ada
        if ($nomorSuratTugas) {
            $query->whereHas('pelaporanHasilAudit.perencanaanAudit', function($q) use ($nomorSuratTugas) {
                $q->where('nomor_surat_tugas', $nomorSuratTugas);
            });
        }
        
        $approvedIss = $query->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_iss' => $item->nomor_iss,
                    'nomor_lha_lhk' => $item->pelaporanHasilAudit->nomor_lha_lhk ?? '-',
                    'hasil_temuan' => $item->hasil_temuan,
                    'permasalahan' => $item->permasalahan
                ];
            });
        
        return response()->json($approvedIss);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelaporan_isi_lha_id' => 'required|exists:pelaporan_temuan,id',
            'rekomendasi' => 'required|string|max:5000',
            'rencana_aksi' => 'required|string|max:5000',
            'eviden_rekomendasi' => 'required|string|max:5000',
            'pic_business_contact' => 'required|exists:master_user,id',
            'pic_approval_1_spi' => 'required|exists:master_user,id',
            'pic_approval_2_spi' => 'required|exists:master_user,id',
            'target_waktu' => 'required|date',
        ]);
        
        // Ambil data user untuk format PIC Rekomendasi (gabungan)
        $picBusinessContact = MasterUser::with('auditee')->find($request->pic_business_contact);
        $picApproval1 = MasterUser::with('auditee')->find($request->pic_approval_1_spi);
        $picApproval2 = MasterUser::with('auditee')->find($request->pic_approval_2_spi);
        
        $picRekomendasiList = [];
        if ($picBusinessContact) {
            $picRekomendasiList[] = 'BUSINESS CONTACT: ' . $picBusinessContact->nama . ' - ' . ($picBusinessContact->auditee->divisi ?? '-');
        }
        if ($picApproval1) {
            $picRekomendasiList[] = 'APPROVAL 1 SPI: ' . $picApproval1->nama . ' - ' . ($picApproval1->auditee->divisi ?? '-');
        }
        if ($picApproval2) {
            $picRekomendasiList[] = 'APPROVAL 2 SPI: ' . $picApproval2->nama . ' - ' . ($picApproval2->auditee->divisi ?? '-');
        }
        $picRekomendasi = implode(' | ', $picRekomendasiList);
        
        // Create record with pelaporan_temuan_id stored in pelaporan_isi_lha_id field for compatibility
        $data = $request->all();
        $data['pic_rekomendasi'] = $picRekomendasi;
        unset($data['pic_business_contact']);
        unset($data['pic_approval_1_spi']);
        unset($data['pic_approval_2_spi']);
        
        $rekomendasi = PenutupLhaRekomendasi::create($data);
        
        // Attach PIC users to rekomendasi dengan pic_type
        $rekomendasi->picUsers()->attach([
            $request->pic_business_contact => ['pic_type' => 'business_contact'],
            $request->pic_approval_1_spi => ['pic_type' => 'approval_1_spi'],
            $request->pic_approval_2_spi => ['pic_type' => 'approval_2_spi'],
        ]);
        
        // Reload dengan relasi untuk mendapatkan nomor surat tugas
        $rekomendasi->load(['temuan.pelaporanHasilAudit.perencanaanAudit']);
        
        // Ambil nomor surat tugas dari temuan
        $nomorSuratTugas = null;
        if ($rekomendasi->temuan && $rekomendasi->temuan->pelaporanHasilAudit && $rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit) {
            $nomorSuratTugas = $rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas;
        }
        
        return redirect()->route('audit.penutup-lha-rekomendasi.index', ['nomor_surat_tugas' => $nomorSuratTugas])
            ->with('success', 'Rekomendasi penutup LHA/LHK berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $item = PenutupLhaRekomendasi::with(['temuan.pelaporanHasilAudit', 'picUsers.auditee'])->findOrFail($id);
        
        // Ambil semua user dari master_user untuk dipilih sebagai PIC
        $picUsers = MasterUser::with(['akses', 'auditee'])
            ->orderBy('nama')
            ->get();
        
        // Ambil PIC berdasarkan pic_type dari pivot table
        $picBusinessContact = $item->picUsers()->wherePivot('pic_type', 'business_contact')->first();
        $picApproval1 = $item->picUsers()->wherePivot('pic_type', 'approval_1_spi')->first();
        $picApproval2 = $item->picUsers()->wherePivot('pic_type', 'approval_2_spi')->first();
        
        $item->pic_business_contact_id = $picBusinessContact ? $picBusinessContact->id : null;
        $item->pic_approval_1_spi_id = $picApproval1 ? $picApproval1->id : null;
        $item->pic_approval_2_spi_id = $picApproval2 ? $picApproval2->id : null;
        
        return view('audit.pelaporan.penutup-lha.edit', compact('item', 'picUsers'));
    }

    public function update(Request $request, $id)
    {
        $item = PenutupLhaRekomendasi::with(['temuan.pelaporanHasilAudit'])->findOrFail($id);
        $request->validate([
            'pelaporan_isi_lha_id' => 'required|exists:pelaporan_temuan,id',
            'rekomendasi' => 'required|string|max:5000',
            'rencana_aksi' => 'required|string|max:5000',
            'eviden_rekomendasi' => 'required|string|max:5000',
            'pic_business_contact' => 'required|exists:master_user,id',
            'pic_approval_1_spi' => 'required|exists:master_user,id',
            'pic_approval_2_spi' => 'required|exists:master_user,id',
            'target_waktu' => 'required|date',
        ]);
        
        // Ambil data user untuk format PIC Rekomendasi (gabungan)
        $picBusinessContact = MasterUser::with('auditee')->find($request->pic_business_contact);
        $picApproval1 = MasterUser::with('auditee')->find($request->pic_approval_1_spi);
        $picApproval2 = MasterUser::with('auditee')->find($request->pic_approval_2_spi);
        
        $picRekomendasiList = [];
        if ($picBusinessContact) {
            $picRekomendasiList[] = 'BUSINESS CONTACT: ' . $picBusinessContact->nama . ' - ' . ($picBusinessContact->auditee->divisi ?? '-');
        }
        if ($picApproval1) {
            $picRekomendasiList[] = 'APPROVAL 1 SPI: ' . $picApproval1->nama . ' - ' . ($picApproval1->auditee->divisi ?? '-');
        }
        if ($picApproval2) {
            $picRekomendasiList[] = 'APPROVAL 2 SPI: ' . $picApproval2->nama . ' - ' . ($picApproval2->auditee->divisi ?? '-');
        }
        $picRekomendasi = implode(' | ', $picRekomendasiList);
        
        $data = $request->all();
        $data['pic_rekomendasi'] = $picRekomendasi;
        unset($data['pic_business_contact']);
        unset($data['pic_approval_1_spi']);
        unset($data['pic_approval_2_spi']);
        
        $item->update($data);
        
        // Sync PIC users to rekomendasi dengan pic_type
        $item->picUsers()->sync([
            $request->pic_business_contact => ['pic_type' => 'business_contact'],
            $request->pic_approval_1_spi => ['pic_type' => 'approval_1_spi'],
            $request->pic_approval_2_spi => ['pic_type' => 'approval_2_spi'],
        ]);
        
        // Ambil nomor surat tugas dari temuan
        $nomorSuratTugas = null;
        if ($item->temuan && $item->temuan->pelaporanHasilAudit && $item->temuan->pelaporanHasilAudit->perencanaanAudit) {
            $nomorSuratTugas = $item->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas;
        }
        
        return redirect()->route('audit.penutup-lha-rekomendasi.index', ['nomor_surat_tugas' => $nomorSuratTugas])
            ->with('success', 'Rekomendasi penutup LHA/LHK berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = PenutupLhaRekomendasi::with(['temuan.pelaporanHasilAudit.perencanaanAudit'])->findOrFail($id);
        
        // Ambil nomor surat tugas dari temuan sebelum delete
        $nomorSuratTugas = null;
        if ($item->temuan && $item->temuan->pelaporanHasilAudit && $item->temuan->pelaporanHasilAudit->perencanaanAudit) {
            $nomorSuratTugas = $item->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas;
        }
        
        $item->delete();
        
        return redirect()->route('audit.penutup-lha-rekomendasi.index', ['nomor_surat_tugas' => $nomorSuratTugas])
            ->with('success', 'Rekomendasi penutup LHA/LHK berhasil dihapus!');
    }

    public function show($id)
    {
        $item = PenutupLhaRekomendasi::with(['approvedBy', 'temuan.pelaporanHasilAudit'])->findOrFail($id);
        return view('audit.pelaporan.penutup-lha.show', compact('item'));
    }

    public function approval(Request $request, $id)
    {
        $item = PenutupLhaRekomendasi::with(['temuan.pelaporanHasilAudit'])->findOrFail($id);
        
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
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    // TINDAK LANJUT
    public function tindakLanjutForm($rekomendasiId)
    {
        $rekomendasi = PenutupLhaRekomendasi::with(['temuan.pelaporanHasilAudit', 'tindakLanjut'])->findOrFail($rekomendasiId);
        return view('audit.pelaporan.penutup-lha.tindak-lanjut-form', compact('rekomendasi'));
    }

    public function storeTindakLanjut(Request $request, $rekomendasiId)
    {
        $request->validate([
            'real_waktu' => 'nullable|date',
            'komentar' => 'required|array|min:1',
            'komentar.*' => 'required|string|min:3',
            'file_eviden' => 'nullable|file|max:2048',
        ]);
        
        $rekomendasi = PenutupLhaRekomendasi::findOrFail($rekomendasiId);
        
        // Filter komentar yang tidak kosong
        $validKomentar = array_filter($request->komentar, function($k) { 
            return trim($k) !== ''; 
        });
        
        // Gabungkan semua komentar menjadi satu string dengan separator
        $combinedKomentar = implode("\n\n---\n\n", $validKomentar);
        
        // Ambil status yang sudah ada, jangan ubah dari form (status hanya bisa diubah dari halaman pemantauan)
        $latestTindakLanjut = $rekomendasi->tindakLanjut()->orderBy('created_at', 'desc')->first();
        $statusTindakLanjut = $latestTindakLanjut ? $latestTindakLanjut->status_tindak_lanjut : ($rekomendasi->status_tindak_lanjut ?? 'open');
        
        // Buat satu record tindak lanjut dengan komentar yang digabungkan
        $data = [
            'real_waktu' => $request->real_waktu,
            'komentar' => $combinedKomentar,
            'status_tindak_lanjut' => $statusTindakLanjut,
            'penutup_lha_rekomendasi_id' => $rekomendasiId,
        ];
        
        // Handle file evidence
        if ($request->hasFile('file_eviden')) {
            $data['file_eviden'] = $request->file('file_eviden')->store('eviden_tindak_lanjut', 'public');
        }
        
        \App\Models\PenutupLhaTindakLanjut::create($data);
        
        // Tidak perlu update status di rekomendasi utama karena status tidak berubah
        // Status hanya bisa diubah melalui halaman pemantauan oleh user yang berwenang
        
        $komentarCount = count($validKomentar);
        
        // Ambil nomor surat tugas untuk redirect
        $nomorSuratTugas = null;
        if ($rekomendasi->temuan && $rekomendasi->temuan->pelaporanHasilAudit && $rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit) {
            $nomorSuratTugas = $rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas;
        }
        
        return redirect()->route('audit.pemantauan.index', ['nomor_surat_tugas' => $nomorSuratTugas])
            ->with('success', "Berhasil menambahkan tindak lanjut dengan {$komentarCount} komentar!");
    }

    public function editTindakLanjut($id)
    {
        $tindakLanjut = PenutupLhaTindakLanjut::with(['rekomendasi.temuan.pelaporanHasilAudit'])->findOrFail($id);
        return view('audit.pelaporan.penutup-lha.tindak-lanjut-edit', compact('tindakLanjut'));
    }

    public function updateTindakLanjut(Request $request, $id)
    {
        $tindakLanjut = PenutupLhaTindakLanjut::with(['rekomendasi.temuan.pelaporanHasilAudit'])->findOrFail($id);
        $request->validate([
            'real_waktu' => 'nullable|date',
            'komentar' => 'nullable|string',
            'file_eviden' => 'nullable|file|max:2048',
            'status_tindak_lanjut' => 'required|in:open,closed,on_progress',
        ]);
        $data = $request->only(['real_waktu', 'komentar', 'status_tindak_lanjut']);
        if ($request->hasFile('file_eviden')) {
            // Hapus file lama jika ada
            if ($tindakLanjut->file_eviden) {
                Storage::disk('public')->delete($tindakLanjut->file_eviden);
            }
            $data['file_eviden'] = $request->file('file_eviden')->store('eviden_tindak_lanjut', 'public');
        }
        $tindakLanjut->update($data);
        
        // Update status tindak lanjut di tabel rekomendasi utama berdasarkan tindak lanjut terbaru
        $rekomendasi = $tindakLanjut->rekomendasi;
        $latestTindakLanjut = $rekomendasi->tindakLanjut()->orderBy('created_at', 'desc')->first();
        if ($latestTindakLanjut && $latestTindakLanjut->id == $tindakLanjut->id) {
            // Jika ini adalah tindak lanjut terbaru, update status di rekomendasi
            $rekomendasi->update([
                'status_tindak_lanjut' => $request->status_tindak_lanjut
            ]);
        }
        
        return redirect()->route('audit.penutup-lha-rekomendasi.show', $tindakLanjut->penutup_lha_rekomendasi_id)
            ->with('success', 'Tindak lanjut berhasil diupdate!');
    }

    public function destroyTindakLanjut($id)
    {
        $tindakLanjut = PenutupLhaTindakLanjut::with(['rekomendasi.temuan.pelaporanHasilAudit'])->findOrFail($id);
        $rekomendasiId = $tindakLanjut->penutup_lha_rekomendasi_id;
        if ($tindakLanjut->file_eviden) {
            Storage::disk('public')->delete($tindakLanjut->file_eviden);
        }
        $tindakLanjut->delete();
        return redirect()->route('audit.penutup-lha-rekomendasi.show', $rekomendasiId)
            ->with('success', 'Tindak lanjut berhasil dihapus!');
    }
} 