<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\Audit\PerencanaanAudit;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardRencanaPkptController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua data PKA dengan relasi
        $pkaData = ProgramKerjaAudit::with([
            'perencanaanAudit.auditee',
            'perencanaanAudit.jenisAudit',
            'milestones',
            'risks',
            'entryMeeting'
        ])->get();

        // Filter berdasarkan bulan jika ada
        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            $pkaData = $pkaData->filter(function($pka) use ($selectedMonth) {
                $pkaMonth = Carbon::parse($pka->tanggal_pka);
                return $pkaMonth->year == $selectedMonth->year && 
                       $pkaMonth->month == $selectedMonth->month;
            });
        }

        // Proses data untuk dashboard
        $dashboardData = [];
        
        foreach ($pkaData as $pka) {
            $perencanaan = $pka->perencanaanAudit;
            $auditee = $perencanaan->auditee ?? null;
            
            // Build auditee name
            $auditeeName = 'Unknown';
            if ($auditee) {
                $direktorat = $auditee->direktorat ?? '';
                $divisiCabang = $auditee->divisi_cabang ?? '';
                $divisi = $auditee->divisi ?? '';
                
                if (!empty($direktorat) || !empty($divisiCabang)) {
                    $auditeeName = trim(trim(($direktorat ?? '') . ' - ' . ($divisiCabang ?? '')));
                    $auditeeName = trim($auditeeName, '- ');
                } elseif (!empty($divisi)) {
                    $auditeeName = $divisi;
                }
            }
            
            // Get jenis audit
            $jenisAudit = 'Audit Operasional'; // Default fallback
            if ($perencanaan->jenisAudit) {
                $jenisAudit = $perencanaan->jenisAudit->nama_jenis_audit ?? $jenisAudit;
            } elseif ($perencanaan->jenis_audit) {
                $jenisAudit = $perencanaan->jenis_audit;
            }
            
            // Get auditor count
            $jumlahAuditor = 1;
            if ($perencanaan->auditor) {
                if (is_array($perencanaan->auditor)) {
                    $jumlahAuditor = count($perencanaan->auditor);
                } elseif (is_string($perencanaan->auditor)) {
                    $decoded = json_decode($perencanaan->auditor, true);
                    $jumlahAuditor = is_array($decoded) ? count($decoded) : 1;
                }
            }
            
            // Get milestone dates
            $rencanaMulai = '-';
            $rencanaSelesai = '-';
            if ($pka->milestones && $pka->milestones->count() > 0) {
                // Cari Entry Meeting milestone
                $entryMilestone = $pka->milestones->where('nama_milestone', 'Entry Meeting')->first();
                if ($entryMilestone && $entryMilestone->tanggal_mulai) {
                    $rencanaMulai = Carbon::parse($entryMilestone->tanggal_mulai)->format('d M Y');
                } else {
                    // Ambil milestone terawal
                    $firstMilestone = $pka->milestones->sortBy('tanggal_mulai')->first();
                    if ($firstMilestone && $firstMilestone->tanggal_mulai) {
                        $rencanaMulai = Carbon::parse($firstMilestone->tanggal_mulai)->format('d M Y');
                    }
                }
                
                // Cari Exit Meeting milestone
                $exitMilestone = $pka->milestones->where('nama_milestone', 'Exit Meeting')->first();
                if ($exitMilestone && $exitMilestone->tanggal_selesai) {
                    $rencanaSelesai = Carbon::parse($exitMilestone->tanggal_selesai)->format('d M Y');
                } else {
                    // Ambil milestone terakhir
                    $lastMilestone = $pka->milestones->sortByDesc('tanggal_selesai')->first();
                    if ($lastMilestone && $lastMilestone->tanggal_selesai) {
                        $rencanaSelesai = Carbon::parse($lastMilestone->tanggal_selesai)->format('d M Y');
                    }
                }
            }
            
            // Get realisasi dates from entry meeting
            $realisasiMulai = '-';
            $realisasiSelesai = '-';
            $statusRealiasi = 'Belum Dimulai';
            
            if ($pka->entryMeeting) {
                if ($pka->entryMeeting->actual_meeting_date) {
                    $realisasiMulai = Carbon::parse($pka->entryMeeting->actual_meeting_date)->format('d M Y');
                    $statusRealiasi = 'Sedang Berlangsung';
                }
            }
            
            // Determine status
            $today = Carbon::now();
            if ($rencanaMulai != '-' && $rencanaSelesai != '-') {
                $startDate = Carbon::parse($rencanaMulai);
                $endDate = Carbon::parse($rencanaSelesai);
                
                if ($realisasiMulai != '-') {
                    $statusRealiasi = 'Sedang Berlangsung';
                } elseif ($today->lt($startDate)) {
                    $statusRealiasi = 'Belum Dimulai';
                } elseif ($today->between($startDate, $endDate)) {
                    $statusRealiasi = 'Sedang Berlangsung';
                } elseif ($today->gt($endDate)) {
                    $statusRealiasi = 'Terlambat';
                }
            }
            
            // Count risks
            $jumlahRisiko = $pka->risks ? $pka->risks->count() : 0;
            
            // Count milestones
            $jumlahMilestone = $pka->milestones ? $pka->milestones->count() : 0;
            
            $dashboardData[] = [
                'id' => $pka->id,
                'no_pka' => $pka->no_pka,
                'tanggal_pka' => Carbon::parse($pka->tanggal_pka)->format('d M Y'),
                'surat_tugas' => $perencanaan->nomor_surat_tugas ?? '-',
                'auditee' => $auditeeName,
                'jenis_audit' => $jenisAudit,
                'jumlah_auditor' => $jumlahAuditor,
                'jumlah_risiko' => $jumlahRisiko,
                'jumlah_milestone' => $jumlahMilestone,
                'rencana_mulai' => $rencanaMulai,
                'rencana_selesai' => $rencanaSelesai,
                'realisasi_mulai' => $realisasiMulai,
                'realisasi_selesai' => $realisasiSelesai,
                'status' => $statusRealiasi,
            ];
        }
        
        // Generate months for calendar view
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create(null, $i, 1)->translatedFormat('M');
            $months[] = $monthName;
        }
        
        // Data untuk pie chart
        $statusSelesai = collect($dashboardData)->where('status', 'Selesai')->count();
        $statusBerlangsung = collect($dashboardData)->where('status', 'Sedang Berlangsung')->count();
        $statusBelum = collect($dashboardData)->where('status', 'Belum Dimulai')->count();
        $statusTerlambat = collect($dashboardData)->where('status', 'Terlambat')->count();
        
        return view('audit.dashboard-rencana-pkpt.index', compact(
            'dashboardData', 
            'months', 
            'pkaData',
            'statusSelesai',
            'statusBerlangsung',
            'statusBelum',
            'statusTerlambat'
        ));
    }
}
