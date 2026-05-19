<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\PenutupLhaRekomendasi;
use App\Models\PenutupLhaTindakLanjut;
use App\Models\Audit\PelaporanTemuan;
use App\Models\MasterData\MasterAuditee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProgressTindakLanjutController extends Controller
{
    public function index(Request $request)
    {
        // Filter
        $selectedYear = $request->filled('tahun') ? $request->tahun : date('Y');
        $selectedStatus = $request->filled('status') ? $request->status : 'all';
        $selectedAuditee = $request->filled('auditee_id') ? $request->auditee_id : null;
        
        // Query dasar
        $query = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'tindakLanjut'
        ]);
        
        // Filter by auditee
        if ($selectedAuditee) {
            $query->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($selectedAuditee) {
                $q->where('auditee_id', $selectedAuditee);
            });
        }
        
        // Jika user adalah AUDITEE, timpa filter auditee dengan auditee_id miliknya
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAuditeeId = \App\Helpers\AuthHelper::getUserAuditeeId();
            if ($userAuditeeId !== null) {
                $query->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($userAuditeeId) {
                    $q->where('auditee_id', $userAuditeeId);
                });
            }
        }
        
        // Filter by status
        if ($selectedStatus != 'all') {
            $query->where('status_tindak_lanjut', $selectedStatus);
        }
        
        $rekomendasiData = $query->get();
        
        // 1. Summary Data
        $totalRekomendasi = PenutupLhaRekomendasi::count();
        $statusOpen = PenutupLhaRekomendasi::where('status_tindak_lanjut', 'open')->count();
        $statusOnProgress = PenutupLhaRekomendasi::where('status_tindak_lanjut', 'on_progress')->count();
        $statusClosed = PenutupLhaRekomendasi::where('status_tindak_lanjut', 'closed')->count();
        
        // 2. Status Distribution untuk Pie Chart
        $statusData = [
            'Open' => $statusOpen,
            'On Progress' => $statusOnProgress,
            'Closed' => $statusClosed
        ];
        
        // 3. Progress per Auditee (Top 10)
        $auditeeProgress = PenutupLhaRekomendasi::with('temuan.pelaporanHasilAudit.perencanaanAudit.auditee')
            ->get()
            ->groupBy(function($item) {
                $auditee = $item->temuan->pelaporanHasilAudit->perencanaanAudit->auditee ?? null;
                if ($auditee) {
                    $direktorat = $auditee->direktorat ?? '';
                    $divisiCabang = $auditee->divisi_cabang ?? '';
                    $divisi = $auditee->divisi ?? '';
                    
                    if (!empty($direktorat) || !empty($divisiCabang)) {
                        return trim(trim(($direktorat ?? '') . ' - ' . ($divisiCabang ?? '')));
                    } elseif (!empty($divisi)) {
                        return $divisi;
                    }
                }
                return 'Unknown';
            })
            ->map(function($group, $auditeeName) {
                return [
                    'name' => $auditeeName,
                    'total' => $group->count(),
                    'open' => $group->where('status_tindak_lanjut', 'open')->count(),
                    'on_progress' => $group->where('status_tindak_lanjut', 'on_progress')->count(),
                    'closed' => $group->where('status_tindak_lanjut', 'closed')->count(),
                ];
            })
            ->sortByDesc('total')
            ->take(10)
            ->values();
        
        // 4. Progress per Bulan (Line Chart)
        $bulananData = [];
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create(null, $i, 1)->translatedFormat('M');
            $months[] = $monthName;
            
            $bulananData[$monthName] = [
                'open' => PenutupLhaRekomendasi::whereYear('created_at', $selectedYear)
                    ->whereMonth('created_at', $i)
                    ->where('status_tindak_lanjut', 'open')->count(),
                'on_progress' => PenutupLhaRekomendasi::whereYear('created_at', $selectedYear)
                    ->whereMonth('created_at', $i)
                    ->where('status_tindak_lanjut', 'on_progress')->count(),
                'closed' => PenutupLhaRekomendasi::whereYear('created_at', $selectedYear)
                    ->whereMonth('created_at', $i)
                    ->where('status_tindak_lanjut', 'closed')->count(),
            ];
        }
        
        // 5. Completion Rate
        $completionRate = $totalRekomendasi > 0 
            ? round(($statusClosed / $totalRekomendasi) * 100, 2) 
            : 0;
        
        // 6. On Time vs Overdue
        $onTimeCount = 0;
        $overdueCount = 0;
        
        foreach ($rekomendasiData as $rekomendasi) {
            if ($rekomendasi->target_waktu && $rekomendasi->real_waktu) {
                $targetDate = Carbon::parse($rekomendasi->target_waktu);
                $realDate = Carbon::parse($rekomendasi->real_waktu);
                
                if ($realDate->lte($targetDate)) {
                    $onTimeCount++;
                } else {
                    $overdueCount++;
                }
            } elseif ($rekomendasi->target_waktu) {
                $targetDate = Carbon::parse($rekomendasi->target_waktu);
                if (Carbon::now()->gt($targetDate) && $rekomendasi->status_tindak_lanjut != 'closed') {
                    $overdueCount++;
                }
            }
        }
        
        // 7. Detail Data untuk Tabel
        $detailData = $rekomendasiData->map(function($item) {
            $auditee = $item->temuan->pelaporanHasilAudit->perencanaanAudit->auditee ?? null;
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
            
            $latestTindakLanjut = $item->tindakLanjut->sortByDesc('created_at')->first();
            $progressPercentage = 0;
            
            if ($item->status_tindak_lanjut == 'closed') {
                $progressPercentage = 100;
            } elseif ($item->status_tindak_lanjut == 'on_progress') {
                $progressPercentage = 50;
            } else {
                $progressPercentage = 0;
            }
            
            return [
                'id' => $item->id,
                'rekomendasi' => $item->rekomendasi,
                'auditee' => $auditeeName,
                'target_waktu' => $item->target_waktu ? Carbon::parse($item->target_waktu)->format('d M Y') : '-',
                'real_waktu' => $item->real_waktu ? Carbon::parse($item->real_waktu)->format('d M Y') : '-',
                'status' => $item->status_tindak_lanjut,
                'progress' => $progressPercentage,
                'latest_update' => $latestTindakLanjut ? $latestTindakLanjut->created_at->format('d M Y') : '-',
            ];
        });
        
        // Get auditee list for filter
        $auditees = MasterAuditee::all();
        
        return view('audit.progress-tindak-lanjut.index', compact(
            'totalRekomendasi',
            'statusOpen',
            'statusOnProgress',
            'statusClosed',
            'statusData',
            'auditeeProgress',
            'bulananData',
            'months',
            'completionRate',
            'onTimeCount',
            'overdueCount',
            'detailData',
            'auditees',
            'selectedYear',
            'selectedStatus',
            'selectedAuditee'
        ));
    }
}
