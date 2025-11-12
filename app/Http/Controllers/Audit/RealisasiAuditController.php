<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\RealisasiAudit;
use App\Models\Audit\PerencanaanAudit;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RealisasiAuditController extends Controller
{
    public function index(Request $request)
    {
        // Fetch exit meeting data (RealisasiAudit) yang sudah diapprove
        $realisasiData = RealisasiAudit::with([
            'perencanaanAudit.auditee',
            'perencanaanAudit.programKerjaAudit.milestones'
        ])->where('status_approval', 'approved');

        // Filter by month if provided
        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            $realisasiData->where(function($query) use ($selectedMonth) {
                $query->whereYear('tanggal_mulai', $selectedMonth->year)
                      ->whereMonth('tanggal_mulai', $selectedMonth->month)
                      ->orWhere(function($q) use ($selectedMonth) {
                          $q->whereYear('tanggal_selesai', $selectedMonth->year)
                            ->whereMonth('tanggal_selesai', $selectedMonth->month);
                      });
            });
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $realisasiData->where('status', $request->status);
        }

        $realisasiData = $realisasiData->get();

        // Group data by auditee and audit type
        $groupedData = [];
        foreach ($realisasiData as $item) {
            if (!$item->perencanaanAudit || !$item->perencanaanAudit->auditee) {
                continue;
            }

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
            $key = $auditeeName . '|' . $jenisAudit;

            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'auditee' => $auditeeName,
                    'jenis_audit' => $jenisAudit,
                    'total_audit' => 0,
                    'selesai' => 0,
                    'on_progress' => 0,
                    'belum_dimulai' => 0,
                    'terlambat' => 0,
                    'details' => []
                ];
            }

            $groupedData[$key]['total_audit']++;
            
            // Count by status
            switch ($item->status) {
                case 'selesai':
                    $groupedData[$key]['selesai']++;
                    break;
                case 'on progress':
                    $groupedData[$key]['on_progress']++;
                    break;
                case 'belum':
                    $groupedData[$key]['belum_dimulai']++;
                    break;
                default:
                    $groupedData[$key]['belum_dimulai']++;
            }

            // Add detail data
            $groupedData[$key]['details'][] = [
                'id' => $item->id,
                'tanggal_mulai' => $item->tanggal_mulai,
                'tanggal_selesai' => $item->tanggal_selesai,
                'status' => $item->status,
                'approved_at' => $item->approved_at,
                'approved_by' => $item->approved_by,
                'rencana_audit_mulai' => $this->getPlanningStartDate($item),
                'rencana_audit_selesai' => $this->getPlanningEndDate($item)
            ];
        }

        // Convert to indexed array
        $groupedData = array_values($groupedData);

        // Get status options for filter
        $statusOptions = [
            'selesai' => 'Selesai',
            'on progress' => 'Sedang Berlangsung',
            'belum' => 'Belum Dimulai'
        ];

        return view('audit.realisasi-audit.index', compact('groupedData', 'statusOptions'));
    }

    /**
     * Get planning start date from milestones
     */
    private function getPlanningStartDate($item)
    {
        if ($item->perencanaanAudit && $item->perencanaanAudit->programKerjaAudit && $item->perencanaanAudit->programKerjaAudit->count() > 0) {
            $pka = $item->perencanaanAudit->programKerjaAudit->first();
            if ($pka->milestones && $pka->milestones->count() > 0) {
                $firstMilestone = $pka->milestones->sortBy('tanggal_mulai')->first();
                if ($firstMilestone) {
                    return Carbon::parse($firstMilestone->tanggal_mulai)->format('d M Y');
                }
            }
        }
        return '-';
    }

    /**
     * Get planning end date from milestones
     */
    private function getPlanningEndDate($item)
    {
        if ($item->perencanaanAudit && $item->perencanaanAudit->programKerjaAudit && $item->perencanaanAudit->programKerjaAudit->count() > 0) {
            $pka = $item->perencanaanAudit->programKerjaAudit->first();
            if ($pka->milestones && $pka->milestones->count() > 0) {
                $lastMilestone = $pka->milestones->sortByDesc('tanggal_selesai')->first();
                if ($lastMilestone) {
                    return Carbon::parse($lastMilestone->tanggal_selesai)->format('d M Y');
                }
            }
        }
        return '-';
    }

    /**
     * Show detailed view for specific auditee and audit type
     */
    public function show($auditeeKey, $jenisAudit)
    {
        $auditeeName = str_replace('_', ' ', $auditeeKey);
        $jenisAuditName = str_replace('_', ' ', $jenisAudit);

        $realisasiData = RealisasiAudit::with([
            'perencanaanAudit.auditee',
            'perencanaanAudit.programKerjaAudit.milestones'
        ])->where('status_approval', 'approved')
        ->whereHas('perencanaanAudit.auditee', function($query) use ($auditeeName) {
            $query->where('divisi', 'like', '%' . $auditeeName . '%')
                  ->orWhere('divisi_cabang', 'like', '%' . $auditeeName . '%')
                  ->orWhere('direktorat', 'like', '%' . $auditeeName . '%');
        })
        ->whereHas('perencanaanAudit', function($query) use ($jenisAuditName) {
            $query->where('jenis_audit', 'like', '%' . $jenisAuditName . '%');
        })->get();

        return view('audit.realisasi-audit.show', compact('realisasiData', 'auditeeName', 'jenisAuditName'));
    }
}









