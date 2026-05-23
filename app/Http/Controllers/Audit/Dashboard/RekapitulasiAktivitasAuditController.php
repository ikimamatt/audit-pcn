<?php

namespace App\Http\Controllers\Audit\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\Audit\PerencanaanAudit;
use App\Models\EntryMeeting;
use App\Models\WalkthroughAudit;
use App\Models\TodBpmAudit;
use App\Models\ToeAudit;
use App\Models\ExitMeetingUpload;
use App\Models\Models\Audit\PelaporanHasilAudit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RekapitulasiAktivitasAuditController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->filled('tahun') ? $request->tahun : date('Y');
        
        // 1. Data Status PKA (Selesai, Sedang Berlangsung, Belum Dimulai, Terlambat)
        $pkaData = ProgramKerjaAudit::with([
            'perencanaanAudit.auditee',
            'milestones',
            'entryMeeting'
        ])->get();
        
        $pkaStatusData = [
            'Selesai' => 0,
            'Sedang Berlangsung' => 0,
            'Belum Dimulai' => 0,
            'Terlambat' => 0
        ];
        
        foreach ($pkaData as $pka) {
            $status = $this->getPkaStatus($pka);
            if (isset($pkaStatusData[$status])) {
                $pkaStatusData[$status]++;
            }
        }
        
        // 2. Data Aktivitas per Jenis (Entry Meeting, Walkthrough, TOD, TOE, Exit Meeting, Pelaporan)
        $aktivitasData = [
            'Entry Meeting' => EntryMeeting::count(),
            'Walkthrough Audit' => WalkthroughAudit::count(),
            'TOD BPM Audit' => TodBpmAudit::count(),
            'TOE Audit' => ToeAudit::count(),
            'Exit Meeting' => ExitMeetingUpload::count(),
            'Pelaporan Hasil Audit' => PelaporanHasilAudit::count(),
        ];
        
        // 3. Data Aktivitas per Bulan (Line Chart)
        $bulananData = [];
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create(null, $i, 1)->translatedFormat('M');
            $months[] = $monthName;
            
            $bulananData[$monthName] = [
                'Entry Meeting' => EntryMeeting::whereYear('created_at', $selectedYear)
                    ->whereMonth('created_at', $i)->count(),
                'Walkthrough' => WalkthroughAudit::whereYear('created_at', $selectedYear)
                    ->whereMonth('created_at', $i)->count(),
                'TOD BPM' => TodBpmAudit::whereYear('created_at', $selectedYear)
                    ->whereMonth('created_at', $i)->count(),
                'TOE' => ToeAudit::whereYear('created_at', $selectedYear)
                    ->whereMonth('created_at', $i)->count(),
                'Exit Meeting' => ExitMeetingUpload::whereYear('created_at', $selectedYear)
                    ->whereMonth('created_at', $i)->count(),
                'Pelaporan' => PelaporanHasilAudit::whereYear('created_at', $selectedYear)
                    ->whereMonth('created_at', $i)->count(),
            ];
        }
        
        // 4. Data Status Approval (Approved, Pending, Rejected)
        $approvalData = [
            'Approved' => 0,
            'Pending' => 0,
            'Rejected' => 0
        ];
        
        // Hitung dari berbagai tabel yang punya status_approval
        $approvalData['Approved'] += EntryMeeting::where('status_approval', 'approved')->count();
        $approvalData['Approved'] += WalkthroughAudit::where('status_approval', 'approved')->count();
        $approvalData['Approved'] += TodBpmAudit::where('status_approval', 'approved')->count();
        $approvalData['Approved'] += ToeAudit::where('status_approval', 'approved')->count();
        // ExitMeetingUpload menggunakan kolom 'approve' (boolean) atau status_approval_undangan/absensi
        $approvalData['Approved'] += ExitMeetingUpload::where('approve', true)->count();
        $approvalData['Approved'] += PelaporanHasilAudit::where('status_approval', 'approved')->count();
        
        $approvalData['Pending'] += EntryMeeting::where('status_approval', 'pending')->count();
        $approvalData['Pending'] += WalkthroughAudit::where('status_approval', 'pending')->count();
        $approvalData['Pending'] += TodBpmAudit::where('status_approval', 'pending')->count();
        $approvalData['Pending'] += ToeAudit::where('status_approval', 'pending')->count();
        // ExitMeetingUpload: pending jika approve = false dan belum ada yang rejected
        $approvalData['Pending'] += ExitMeetingUpload::where('approve', false)
            ->where(function($query) {
                $query->where('status_approval_undangan', '!=', 'rejected')
                      ->where('status_approval_absensi', '!=', 'rejected');
            })->count();
        $approvalData['Pending'] += PelaporanHasilAudit::where('status_approval', 'pending')->count();
        
        $approvalData['Rejected'] += EntryMeeting::where('status_approval', 'rejected')->count();
        $approvalData['Rejected'] += WalkthroughAudit::where('status_approval', 'rejected')->count();
        $approvalData['Rejected'] += TodBpmAudit::where('status_approval', 'rejected')->count();
        $approvalData['Rejected'] += ToeAudit::where('status_approval', 'rejected')->count();
        // ExitMeetingUpload: rejected jika ada yang rejected di undangan atau absensi
        $approvalData['Rejected'] += ExitMeetingUpload::where(function($query) {
            $query->where('status_approval_undangan', 'rejected')
                  ->orWhere('status_approval_absensi', 'rejected');
        })->count();
        $approvalData['Rejected'] += PelaporanHasilAudit::where('status_approval', 'rejected')->count();
        
        // 5. Data per Auditee (Top 10)
        $auditeeData = PerencanaanAudit::with('auditee')
            ->select('auditee_id', DB::raw('count(*) as total'))
            ->groupBy('auditee_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $auditee = $item->auditee;
                $name = 'Unknown';
                if ($auditee) {
                    $direktorat = $auditee->direktorat ?? '';
                    $divisiCabang = $auditee->divisi_cabang ?? '';
                    $divisi = $auditee->divisi ?? '';
                    
                    if (!empty($direktorat) || !empty($divisiCabang)) {
                        $name = trim(trim(($direktorat ?? '') . ' - ' . ($divisiCabang ?? '')));
                        $name = trim($name, '- ');
                    } elseif (!empty($divisi)) {
                        $name = $divisi;
                    }
                }
                return [
                    'name' => $name,
                    'total' => $item->total
                ];
            });
        
        // 6. Total Summary
        $totalSummary = [
            'total_pka' => ProgramKerjaAudit::count(),
            'total_perencanaan' => PerencanaanAudit::count(),
            'total_entry_meeting' => EntryMeeting::count(),
            'total_walkthrough' => WalkthroughAudit::count(),
            'total_tod' => TodBpmAudit::count(),
            'total_toe' => ToeAudit::count(),
            'total_exit' => ExitMeetingUpload::count(),
            'total_pelaporan' => PelaporanHasilAudit::count(),
        ];
        
        return view('audit.rekapitulasi-aktivitas.index', compact(
            'pkaStatusData',
            'aktivitasData',
            'bulananData',
            'months',
            'approvalData',
            'auditeeData',
            'totalSummary',
            'selectedYear'
        ));
    }
    
    private function getPkaStatus($pka)
    {
        $today = Carbon::now();
        $rencanaMulai = null;
        $rencanaSelesai = null;
        
        if ($pka->milestones && $pka->milestones->count() > 0) {
            $entryMilestone = $pka->milestones->where('nama_milestone', 'Entry Meeting')->first();
            if ($entryMilestone && $entryMilestone->tanggal_mulai) {
                $rencanaMulai = Carbon::parse($entryMilestone->tanggal_mulai);
            } else {
                $firstMilestone = $pka->milestones->sortBy('tanggal_mulai')->first();
                if ($firstMilestone && $firstMilestone->tanggal_mulai) {
                    $rencanaMulai = Carbon::parse($firstMilestone->tanggal_mulai);
                }
            }
            
            $exitMilestone = $pka->milestones->where('nama_milestone', 'Exit Meeting')->first();
            if ($exitMilestone && $exitMilestone->tanggal_selesai) {
                $rencanaSelesai = Carbon::parse($exitMilestone->tanggal_selesai);
            } else {
                $lastMilestone = $pka->milestones->sortByDesc('tanggal_selesai')->first();
                if ($lastMilestone && $lastMilestone->tanggal_selesai) {
                    $rencanaSelesai = Carbon::parse($lastMilestone->tanggal_selesai);
                }
            }
        }
        
        // Check if there's entry meeting with actual date
        if ($pka->entryMeeting && $pka->entryMeeting->actual_meeting_date) {
            return 'Sedang Berlangsung';
        }
        
        if ($rencanaMulai && $rencanaSelesai) {
            if ($today->lt($rencanaMulai)) {
                return 'Belum Dimulai';
            } elseif ($today->between($rencanaMulai, $rencanaSelesai)) {
                return 'Sedang Berlangsung';
            } elseif ($today->gt($rencanaSelesai)) {
                // Check if there's exit meeting or completion
                if ($pka->entryMeeting && $pka->entryMeeting->actual_meeting_date) {
                    return 'Selesai';
                }
                return 'Terlambat';
            }
        }
        
        return 'Belum Dimulai';
    }
}
