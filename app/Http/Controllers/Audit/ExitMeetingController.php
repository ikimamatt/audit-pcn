<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\RealisasiAudit;
use App\Models\Audit\PerencanaanAudit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ExitMeetingController extends Controller
{
    public function index(Request $request)
    {
        $realisasiAudits = RealisasiAudit::with([
            'perencanaanAudit.auditee',
            'perencanaanAudit.programKerjaAudit.milestones'
        ])
            ->when($request->filled('bulan'), function ($query) use ($request) {
                $selectedMonth = Carbon::parse($request->bulan);
                $query->whereHas('perencanaanAudit', function ($q) use ($selectedMonth) {
                    $q->whereYear('tanggal_audit_mulai', $selectedMonth->year)
                        ->whereMonth('tanggal_audit_mulai', $selectedMonth->month);
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('audit.exit-meeting.index', compact('realisasiAudits'));
    }

    public function create()
    {
        // Get all perencanaan audit with their PKA and entry meetings
        $allPerencanaanAudits = PerencanaanAudit::with(['auditee', 'programKerjaAudit.entryMeeting'])->get();
        
        $perencanaanAudits = $allPerencanaanAudits->filter(function($perencanaan) {
            // Jika tidak ada PKA, bisa digunakan
            if ($perencanaan->programKerjaAudit->isEmpty()) {
                return true;
            }
            
            // Check setiap PKA untuk entry meeting
            foreach ($perencanaan->programKerjaAudit as $pka) {
                // Jika PKA tidak memiliki entry meeting, bisa digunakan
                if (!$pka->entryMeeting) {
                    return true;
                }
                
                // Jika entry meeting di-reject, bisa digunakan
                if ($pka->entryMeeting->status === 'rejected') {
                    return true;
                }
            }
            
            return false;
        });
            
        return view('audit.exit-meeting.create', compact('perencanaanAudits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai', // Tanggal selesai harus setelah atau sama dengan tanggal mulai
            'file_undangan' => 'nullable|file|mimes:pdf|max:2048',
            'file_absensi' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $data = $request->all();
        
        // Handle file uploads
        if ($request->hasFile('file_undangan')) {
            $data['file_undangan'] = $request->file('file_undangan')->store('exit_meeting/undangan', 'public');
        }
        
        if ($request->hasFile('file_absensi')) {
            $data['file_absensi'] = $request->file('file_absensi')->store('exit_meeting/absensi', 'public');
        }

        $realisasiAudit = RealisasiAudit::create($data);
        
        // Update status berdasarkan tanggal secara otomatis
        $this->updateStatusBasedOnDates($realisasiAudit);
        $realisasiAudit->save();

        return redirect()->route('audit.exit-meeting.index')
                        ->with('success', 'Data exit meeting berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $realisasiAudit = RealisasiAudit::findOrFail($id);
        $perencanaanAudits = PerencanaanAudit::with('auditee')->get();
        return view('audit.exit-meeting.edit', compact('realisasiAudit', 'perencanaanAudits'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai', // Tanggal selesai harus setelah atau sama dengan tanggal mulai
            'file_undangan' => 'nullable|file|mimes:pdf|max:2048',
            'file_absensi' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $data = $request->all();

        $realisasiAudit = RealisasiAudit::findOrFail($id);

        // Handle file uploads
        if ($request->hasFile('file_undangan')) {
            // Delete old file if exists
            if ($realisasiAudit->file_undangan) {
                \Storage::disk('public')->delete($realisasiAudit->file_undangan);
            }
            $data['file_undangan'] = $request->file('file_undangan')->store('exit_meeting/undangan', 'public');
        }
        
        if ($request->hasFile('file_absensi')) {
            // Delete old file if exists
            if ($realisasiAudit->file_absensi) {
                \Storage::disk('public')->delete($realisasiAudit->file_absensi);
            }
            $data['file_absensi'] = $request->file('file_absensi')->store('exit_meeting/absensi', 'public');
        }

        $realisasiAudit->update($data);
        
        // Update status berdasarkan tanggal secara otomatis
        $this->updateStatusBasedOnDates($realisasiAudit);
        $realisasiAudit->save();

        return redirect()->route('audit.exit-meeting.index')
                        ->with('success', 'Data exit meeting berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $realisasiAudit = RealisasiAudit::findOrFail($id);
            \Log::info("Attempting to delete RealisasiAudit ID: " . $id);
            
            $deleted = $realisasiAudit->delete();
            \Log::info("Delete result: " . ($deleted ? 'SUCCESS' : 'FAILED'));
            
            if ($deleted) {
                \Log::info("RealisasiAudit ID {$id} successfully deleted");
                return redirect()->route('audit.exit-meeting.index')
                                ->with('success', 'Data exit meeting berhasil dihapus.');
            } else {
                \Log::error("Failed to delete RealisasiAudit ID: " . $id);
                return redirect()->route('audit.exit-meeting.index')
                                ->with('error', 'Gagal menghapus data exit meeting.');
            }
        } catch (\Exception $e) {
            \Log::error("Error deleting RealisasiAudit ID {$id}: " . $e->getMessage());
            return redirect()->route('audit.exit-meeting.index')
                            ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function pie()
    {
        // Ambil data untuk chart pie
        $statusCounts = RealisasiAudit::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Siapkan data untuk chart
        $labels = array_keys($statusCounts);
        $data = array_values($statusCounts);
        $colors = ['#28a745', '#ffc107', '#dc3545']; // Hijau untuk selesai, Kuning untuk on progress, Merah untuk belum

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors
        ]);
    }

    public function chart()
    {
        // Ambil data realisasi audit dengan relasi
        $tabel = RealisasiAudit::with('perencanaanAudit.auditee')->get();
        
        // Debug: Log data untuk memeriksa relasi
        \Log::info('RealisasiAudit data:', $tabel->toArray());
        
        // Hitung jumlah berdasarkan status
        $belum = RealisasiAudit::where('status', 'belum')->count();
        $selesai = RealisasiAudit::where('status', 'selesai')->count();
        $onprogress = RealisasiAudit::where('status', 'on progress')->count();
        
        // Periode untuk judul
        $periode = Carbon::now()->translatedFormat('F Y');
        
        return view('audit.exit-meeting.pie', compact('tabel', 'belum', 'selesai', 'onprogress', 'periode'));
    }

    public function approval($id, Request $request)
    {
        $item = RealisasiAudit::findOrFail($id);
        
        if ($request->action == 'approve') {
            $item->status_approval = 'approved';
            $item->approved_by = auth()->id();
            $item->approved_at = now();
            
            // Auto update status menjadi 'selesai' ketika diapprove
            $item->status = 'selesai';
            
            // Jika belum ada tanggal selesai, set tanggal selesai = tanggal sekarang
            if (!$item->tanggal_selesai) {
                $item->tanggal_selesai = now()->toDateString();
            }
            
            $item->save();
            return redirect()->back()->with('success', 'Exit Meeting berhasil diapprove dan status diubah menjadi Selesai!');
        } elseif ($request->action == 'reject') {
            // Validasi alasan penolakan
            $request->validate([
                'rejection_reason' => 'required|string|min:10',
            ], [
                'rejection_reason.required' => 'Alasan penolakan harus diisi',
                'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter',
            ]);

            $item->status_approval = 'rejected';
            $item->approved_by = auth()->id();
            $item->approved_at = now();
            $item->rejection_reason = $request->rejection_reason;
            
            // Ketika di-reject, status tetap sesuai kondisi tanggal
            $this->updateStatusBasedOnDates($item);
            
            $item->save();
            return redirect()->back()->with('success', 'Exit Meeting berhasil ditolak dengan alasan: ' . $request->rejection_reason);
        }

        return redirect()->back()->with('error', 'Aksi tidak valid!');
    }
    
    /**
     * Update status berdasarkan tanggal secara otomatis
     */
    private function updateStatusBasedOnDates($item)
    {
        if ($item->tanggal_mulai && $item->tanggal_selesai) {
            $item->status = 'selesai';
        } elseif ($item->tanggal_mulai && !$item->tanggal_selesai) {
            $item->status = 'on progress';
        } else {
            $item->status = 'belum';
        }
    }
} 