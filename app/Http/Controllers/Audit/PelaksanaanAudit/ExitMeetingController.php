<?php

namespace App\Http\Controllers\Audit\PelaksanaanAudit;

use App\Http\Controllers\Controller;
use App\Models\RealisasiAudit;
use App\Models\Audit\PerencanaanAudit;
use Illuminate\Http\Request;
use App\Http\Requests\Audit\PelaksanaanAudit\StoreExitMeetingRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\UpdateExitMeetingRequest;
use App\Http\Requests\Audit\PelaporanAudit\ApprovalRequest;
use App\Services\Audit\ExitMeetingService;
use Carbon\Carbon;

class ExitMeetingController extends Controller
{
    protected $exitMeetingService;

    public function __construct(ExitMeetingService $exitMeetingService)
    {
        $this->exitMeetingService = $exitMeetingService;
    }

    public function index(Request $request)
    {
        $query = RealisasiAudit::with([
            'perencanaanAudit.auditee',
            'perencanaanAudit.programKerjaAudit.milestones'
        ]);

        // Filter by specific ID from details page
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        // Filter by user's divisi/cabang (except for KSPI, ASMAN KSPI, Auditor)
        $userAuditeeId = \App\Helpers\AuthHelper::getUserAuditeeId();
        if ($userAuditeeId !== null) {
            $query->whereHas('perencanaanAudit', function ($q) use ($userAuditeeId) {
                $q->where('auditee_id', $userAuditeeId);
            });
        }

        $query->when($request->filled('bulan'), function ($q) use ($request) {
            $selectedMonth = Carbon::parse($request->bulan);
            $q->whereHas('perencanaanAudit', function ($subQ) use ($selectedMonth) {
                $subQ->whereYear('tanggal_audit_mulai', $selectedMonth->year)
                     ->whereMonth('tanggal_audit_mulai', $selectedMonth->month);
            });
        });

        $realisasiAudits = $query->orderByDesc('id')->get();

        return view('audit.exit-meeting.index', compact('realisasiAudits'));
    }

    public function create()
    {
        // Ambil Perencanaan Audit yang memiliki PKA dengan milestone 'Exit Meeting'
        // dan belum memiliki RealisasiAudit (Exit Meeting) yang statusnya pending atau approved
        try {
            $query = PerencanaanAudit::whereHas('programKerjaAudit.milestones', function($q) {
                $q->where('nama_milestone', 'Exit Meeting');
            })
            ->whereDoesntHave('realisasiAudit', function($q) {
                $q->whereIn('status_approval', ['approved', 'pending']);
            })
            ->with([
                'auditee', 
                'programKerjaAudit.milestones' => function($q) {
                    $q->where('nama_milestone', 'Exit Meeting');
                },
                'realisasiAudit' => function($q) {
                    $q->where('status_approval', 'rejected');
                }
            ]);
        } catch (\Exception $e) {
            // Fallback jika terjadi kegagalan query
            $query = PerencanaanAudit::with(['auditee', 'programKerjaAudit']);
        }
        
        $userAuditeeId = \App\Helpers\AuthHelper::getUserAuditeeId();
        if ($userAuditeeId !== null) {
            $query->where('auditee_id', $userAuditeeId);
        }
        
        $perencanaanAudits = $query->orderBy('nomor_surat_tugas')->get();
            
        return view('audit.exit-meeting.create', compact('perencanaanAudits'));
    }

    public function store(StoreExitMeetingRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('file_undangan')) {
            $data['file_undangan_file'] = $request->file('file_undangan');
        }
        if ($request->hasFile('file_absensi')) {
            $data['file_absensi_file'] = $request->file('file_absensi');
        }

        $this->exitMeetingService->create($data);

        return redirect()->route('audit.exit-meeting.index')
                        ->with('success', 'Data exit meeting berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $realisasiAudit = RealisasiAudit::findOrFail($id);
        $perencanaanAudits = PerencanaanAudit::with('auditee')->get();
        return view('audit.exit-meeting.edit', compact('realisasiAudit', 'perencanaanAudits'));
    }

    public function update(UpdateExitMeetingRequest $request, $id)
    {
        $realisasiAudit = RealisasiAudit::findOrFail($id);
        
        $data = $request->validated();
        if ($request->hasFile('file_undangan')) {
            $data['file_undangan_file'] = $request->file('file_undangan');
        }
        if ($request->hasFile('file_absensi')) {
            $data['file_absensi_file'] = $request->file('file_absensi');
        }

        $this->exitMeetingService->update($realisasiAudit, $data);

        return redirect()->route('audit.exit-meeting.index')
                        ->with('success', 'Data exit meeting berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $realisasiAudit = RealisasiAudit::findOrFail($id);
            $this->exitMeetingService->delete($realisasiAudit);
            
            return redirect()->route('audit.exit-meeting.index')
                            ->with('success', 'Data exit meeting berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('audit.exit-meeting.index')
                            ->with('error', 'Terjadi kesalahan saat menghapus data.');
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
        
        // Hitung jumlah berdasarkan status
        $belum = RealisasiAudit::where('status', 'belum')->count();
        $selesai = RealisasiAudit::where('status', 'selesai')->count();
        $onprogress = RealisasiAudit::where('status', 'on progress')->count();
        
        // Periode untuk judul
        $periode = Carbon::now()->translatedFormat('F Y');
        
        return view('audit.exit-meeting.pie', compact('tabel', 'belum', 'selesai', 'onprogress', 'periode'));
    }

    public function approval($id, ApprovalRequest $request)
    {
        $item = RealisasiAudit::findOrFail($id);

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $item,
            $request->action,
            $request->rejection_reason ?? null
        );

        if ($result['success']) {
            // Refresh model untuk mendapatkan status terbaru
            $item->refresh();
            
            // Jika approve final (level 2), update status menjadi 'selesai'
            if ($request->action == 'approve' && $item->status_approval === 'approved') {
                $item->status = 'selesai';
                if (!$item->tanggal_selesai) {
                    $item->tanggal_selesai = now()->toDateString();
                }
                $item->save();
                return redirect()->back()->with('success', $result['message'] . ' Status diubah menjadi Selesai!');
            }
            
            // Jika reject, update status berdasarkan tanggal
            if ($request->action == 'reject') {
                $this->exitMeetingService->updateStatusBasedOnDates($item);
                $item->save();
            }
            
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
} 