<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\RealisasiAudit;
use App\Models\Audit\PerencanaanAudit;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardPelaksanaanAuditController extends Controller
{
    public function index(Request $request)
    {
        // Fetch exit meeting data (RealisasiAudit) with relations
        $exitMeetingData = RealisasiAudit::with([
            'perencanaanAudit.auditee',
            'perencanaanAudit.programKerjaAudit.milestones'
        ]);

        // Filter by month if provided
        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            $exitMeetingData->where(function($query) use ($selectedMonth) {
                $query->whereYear('tanggal_mulai', $selectedMonth->year)
                      ->whereMonth('tanggal_mulai', $selectedMonth->month)
                      ->orWhere(function($q) use ($selectedMonth) {
                          $q->whereYear('tanggal_selesai', $selectedMonth->year)
                            ->whereMonth('tanggal_selesai', $selectedMonth->month);
                      });
            });
        }

        $exitMeetingData = $exitMeetingData->get();

        $dashboardData = [];
        $months = [];

        // Generate months for the current year
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create(null, $i, 1)->translatedFormat('M'); // e.g., Jan, Feb
            $months[] = $monthName;
        }

        foreach ($exitMeetingData as $item) {
            if (!$item->perencanaanAudit || !$item->perencanaanAudit->auditee) {
                continue; // Skip if no related data
            }

            // Build auditee display name
            $auditee = $item->perencanaanAudit->auditee;
            $direktorat = data_get($auditee, 'direktorat');
            $divisiCabang = data_get($auditee, 'divisi_cabang');
            $divisi = data_get($auditee, 'divisi');

            if (!empty($direktorat) || !empty($divisiCabang)) {
                $auditeeName = trim(trim(($direktorat ?? '') . ' - ' . ($divisiCabang ?? '')));
                $auditeeName = trim($auditeeName, '- ');
            } elseif (!empty($divisi)) {
                $auditeeName = $divisi;
            } else {
                $auditeeName = '-';
            }

            $jenisAudit = $item->perencanaanAudit->jenis_audit ?? '-';
            
            // Get planning dates from milestones if available
            $planningStart = '-';
            $planningFinish = '-';
            
            if ($item->perencanaanAudit->programKerjaAudit && $item->perencanaanAudit->programKerjaAudit->count() > 0) {
                $pka = $item->perencanaanAudit->programKerjaAudit->first();
                if ($pka->milestones && $pka->milestones->count() > 0) {
                    $firstMilestone = $pka->milestones->sortBy('tanggal_mulai')->first();
                    $lastMilestone = $pka->milestones->sortByDesc('tanggal_selesai')->first();
                    
                    if ($firstMilestone) {
                        $planningStart = Carbon::parse($firstMilestone->tanggal_mulai)->format('d M Y');
                    }
                    if ($lastMilestone) {
                        $planningFinish = Carbon::parse($lastMilestone->tanggal_selesai)->format('d M Y');
                    }
                }
            }

            // Get realization dates
            $realisasiStart = $item->tanggal_mulai ? Carbon::parse($item->tanggal_mulai)->format('d M Y') : '-';
            $realisasiFinish = $item->tanggal_selesai ? Carbon::parse($item->tanggal_selesai)->format('d M Y') : '-';

            // Determine status based on approval and realization
            $status = $this->determineStatus($item);

            $key = $auditeeName . '|' . $jenisAudit; // Unique key for grouping

            if (!isset($dashboardData[$key])) {
                $dashboardData[$key] = [
                    'auditee' => $auditeeName,
                    'jenis_audit' => $jenisAudit,
                    'rencana_audit_mulai' => $planningStart,
                    'rencana_audit_selesai' => $planningFinish,
                    'realisasi_audit_mulai' => $realisasiStart,
                    'realisasi_audit_selesai' => $realisasiFinish,
                    'status_realisasi' => $status,
                    'status_approval' => $item->status_approval ?? 'pending',
                    'schedule' => array_fill_keys($months, []), // Initialize schedule for all months
                ];
            }

            // Populate months with audit schedule
            if ($item->tanggal_mulai && $item->tanggal_selesai) {
                $startDate = Carbon::parse($item->tanggal_mulai);
                $endDate = Carbon::parse($item->tanggal_selesai);

                foreach ($months as $month) {
                    $monthNum = Carbon::parse($month)->month;

                    // Check if the audit period overlaps with the current month
                    if (($startDate->month <= $monthNum && $startDate->year <= $endDate->year) &&
                        ($endDate->month >= $monthNum && $endDate->year >= $startDate->year)) {
                        $dashboardData[$key]['schedule'][$month][] = $item->id;
                    }
                }
            }
        }

        // Convert associative array to indexed array for easier iteration in Blade
        $dashboardData = array_values($dashboardData);

        return view('audit.dashboard-pelaksanaan-audit.index', compact('dashboardData', 'months'));
    }

    private function determineStatus($item)
    {
        $today = Carbon::now();
        
        if ($item->tanggal_mulai && $item->tanggal_selesai) {
            $startDate = Carbon::parse($item->tanggal_mulai);
            $endDate = Carbon::parse($item->tanggal_selesai);
            
            if ($today->lt($startDate)) {
                return 'Belum Dimulai';
            } elseif ($today->between($startDate, $endDate)) {
                return 'Sedang Berlangsung';
            } elseif ($today->gt($endDate)) {
                // Check if it's late based on planning dates
                if ($item->perencanaanAudit && $item->perencanaanAudit->programKerjaAudit && $item->perencanaanAudit->programKerjaAudit->count() > 0) {
                    $pka = $item->perencanaanAudit->programKerjaAudit->first();
                    if ($pka->milestones && $pka->milestones->count() > 0) {
                        $lastMilestone = $pka->milestones->sortByDesc('tanggal_selesai')->first();
                        if ($lastMilestone && $today->gt(Carbon::parse($lastMilestone->tanggal_selesai))) {
                            return 'Terlambat';
                        }
                    }
                }
                return 'Selesai';
            }
        } elseif ($item->tanggal_mulai && !$item->tanggal_selesai) {
            return 'Sedang Berlangsung';
        } else {
            return 'Belum Dimulai';
        }

        return 'Belum Dimulai';
    }
}









