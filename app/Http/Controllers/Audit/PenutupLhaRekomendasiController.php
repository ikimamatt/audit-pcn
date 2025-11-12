<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\PenutupLhaRekomendasi;
use App\Models\PenutupLhaTindakLanjut;
// use App\Models\Models\Audit\PelaporanIsiLha;
use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\Audit\PelaporanTemuan;
use App\Models\MasterData\MasterUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PenutupLhaRekomendasiController extends Controller
{
    public function index(Request $request)
    {
        $isiLhaId = $request->get('pelaporan_isi_lha_id');
        
        // Get all data first with relationships
        $query = PenutupLhaRekomendasi::with(['approvedBy', 'temuan.pelaporanHasilAudit']);
        
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
        return view('audit.pelaporan.penutup-lha.index', compact('data', 'isiLhaId'));
    }

    public function create(Request $request)
    {
        $isiLhaId = $request->get('pelaporan_isi_lha_id');
        
        // Get approved ISS data from PelaporanTemuan
        $approvedIss = PelaporanTemuan::where('status_approval', 'approved')
            ->with(['pelaporanHasilAudit'])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_iss' => $item->nomor_iss,
                    'nomor_lha_lhk' => $item->pelaporanHasilAudit->nomor_lha_lhk ?? '-',
                    'hasil_temuan' => $item->hasil_temuan,
                    'permasalahan' => $item->permasalahan
                ];
            });
        
        return view('audit.pelaporan.penutup-lha.create', compact('isiLhaId', 'approvedIss'));
    }

    public function getIssData()
    {
        // Get approved ISS data for dropdown
        $approvedIss = PelaporanTemuan::where('status_approval', 'approved')
            ->with(['pelaporanHasilAudit'])
            ->get()
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
            'pic_rekomendasi' => 'required|string|max:500',
            'target_waktu' => 'required|date',
        ]);
        
        // Create record with pelaporan_temuan_id stored in pelaporan_isi_lha_id field for compatibility
        $data = $request->all();
        
        PenutupLhaRekomendasi::create($data);
        return redirect()->route('audit.penutup-lha-rekomendasi.index')
            ->with('success', 'Rekomendasi penutup LHA/LHK berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $item = PenutupLhaRekomendasi::with(['temuan.pelaporanHasilAudit'])->findOrFail($id);
        // $isiLhaList = PelaporanIsiLha::all();
        return view('audit.pelaporan.penutup-lha.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = PenutupLhaRekomendasi::with(['temuan.pelaporanHasilAudit'])->findOrFail($id);
        $request->validate([
            'pelaporan_isi_lha_id' => 'required|exists:pelaporan_temuan,id',
            'rekomendasi' => 'required|string|max:5000',
            'rencana_aksi' => 'required|string|max:5000',
            'eviden_rekomendasi' => 'required|string|max:5000',
            'pic_rekomendasi' => 'required|string|max:500',
            'target_waktu' => 'required|date',
        ]);
        $item->update($request->all());
        return redirect()->route('audit.penutup-lha-rekomendasi.index', ['pelaporan_isi_lha_id' => $item->pelaporan_isi_lha_id])
            ->with('success', 'Rekomendasi penutup LHA/LHK berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = PenutupLhaRekomendasi::with(['temuan.pelaporanHasilAudit'])->findOrFail($id);
        $isiLhaId = $item->pelaporan_isi_lha_id;
        $item->delete();
        return redirect()->route('audit.penutup-lha-rekomendasi.index', ['pelaporan_isi_lha_id' => $isiLhaId])
            ->with('success', 'Rekomendasi penutup LHA/LHK berhasil dihapus!');
    }

    public function show($id)
    {
        $item = PenutupLhaRekomendasi::with(['approvedBy', 'temuan.pelaporanHasilAudit'])->findOrFail($id);
        return view('audit.pelaporan.penutup-lha.show', compact('item'));
    }

    public function approval(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject'
        ]);
        $item = PenutupLhaRekomendasi::with(['temuan.pelaporanHasilAudit'])->findOrFail($id);
        if ($request->action === 'approve') {
            $item->update([
                'status_approval' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);
            $message = 'Rekomendasi berhasil diapprove!';
        } else {
            $request->validate([
                'alasan_reject' => 'required|string|max:1000'
            ]);
            $item->update([
                'status_approval' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'alasan_reject' => $request->alasan_reject
            ]);
            $message = 'Rekomendasi berhasil direject!';
        }
        return redirect()->back()->with('success', $message);
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
            'status_tindak_lanjut' => 'nullable|in:open,on_progress,closed',
        ]);
        
        $rekomendasi = PenutupLhaRekomendasi::findOrFail($rekomendasiId);
        
        // Filter komentar yang tidak kosong
        $validKomentar = array_filter($request->komentar, function($k) { 
            return trim($k) !== ''; 
        });
        
        // Gabungkan semua komentar menjadi satu string dengan separator
        $combinedKomentar = implode("\n\n---\n\n", $validKomentar);
        
        $statusTindakLanjut = $request->status_tindak_lanjut ?? 'open';
        
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
        
        // Update status tindak lanjut di tabel rekomendasi utama berdasarkan tindak lanjut terbaru
        $rekomendasi->update([
            'status_tindak_lanjut' => $statusTindakLanjut
        ]);
        
        $komentarCount = count($validKomentar);
        
        return redirect()->route('audit.pemantauan.index')
            ->with('success', "Berhasil menambahkan tindak lanjut dengan {$komentarCount} komentar! Status: " . ucfirst(str_replace('_', ' ', $statusTindakLanjut)));
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