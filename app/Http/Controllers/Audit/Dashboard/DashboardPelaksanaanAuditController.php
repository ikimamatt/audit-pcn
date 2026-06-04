<?php

namespace App\Http\Controllers\Audit\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardPelaksanaanAuditController extends Controller
{
    public function index(Request $request)
    {
        // Raw JOIN query replaces Eloquent get() + foreach loop
        $query = DB::table('realisasi_audits as ra')
            ->join('perencanaan_audit as pa', 'ra.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->leftJoin('program_kerja_audit as pka', 'pka.perencanaan_audit_id', '=', 'pa.id')
            ->select(
                'ra.id',
                'ra.tanggal_mulai',
                'ra.tanggal_selesai',
                'ra.status_approval',
                'ma.nama_bidang',
                'pa.jenis_audit',
                DB::raw('(SELECT MIN(m.tanggal_mulai) FROM pka_milestone m WHERE m.program_kerja_audit_id = pka.id) as plan_start'),
                DB::raw('(SELECT MAX(m.tanggal_selesai) FROM pka_milestone m WHERE m.program_kerja_audit_id = pka.id) as plan_end')
            )
            // Deduplicate when perencanaan has multiple PKAs
            ->groupBy(
                'ra.id', 'ra.tanggal_mulai', 'ra.tanggal_selesai', 'ra.status_approval',
                'ma.nama_bidang', 'pa.jenis_audit', 'pka.id'
            );

        // Filter by month
        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            $query->where(function ($q) use ($selectedMonth) {
                $q->where(function ($q2) use ($selectedMonth) {
                    $q2->whereYear('ra.tanggal_mulai', $selectedMonth->year)
                       ->whereMonth('ra.tanggal_mulai', $selectedMonth->month);
                })->orWhere(function ($q2) use ($selectedMonth) {
                    $q2->whereYear('ra.tanggal_selesai', $selectedMonth->year)
                       ->whereMonth('ra.tanggal_selesai', $selectedMonth->month);
                });
            });
        }

        $rows = $query->get();

        // Generate months
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create(null, $i, 1)->translatedFormat('M');
        }

        $dashboardData = [];
        $today = Carbon::now();

        foreach ($rows as $item) {
            // Build auditee name
            $auditeeName = $item->nama_bidang ?? '-';

            $jenisAudit = $item->jenis_audit ?? '-';

            // Planning dates from milestone subqueries (no collection sorting needed)
            $planningStart = $item->plan_start ? Carbon::parse($item->plan_start)->format('d M Y') : '-';
            $planningFinish = $item->plan_end ? Carbon::parse($item->plan_end)->format('d M Y') : '-';

            // Realization dates
            $realisasiStart = $item->tanggal_mulai ? Carbon::parse($item->tanggal_mulai)->format('d M Y') : '-';
            $realisasiFinish = $item->tanggal_selesai ? Carbon::parse($item->tanggal_selesai)->format('d M Y') : '-';

            // Determine status
            $status = 'Belum Dimulai';
            if ($item->tanggal_mulai && $item->tanggal_selesai) {
                $startDate = Carbon::parse($item->tanggal_mulai);
                $endDate = Carbon::parse($item->tanggal_selesai);

                if ($today->lt($startDate)) {
                    $status = 'Belum Dimulai';
                } elseif ($today->between($startDate, $endDate)) {
                    $status = 'Sedang Berlangsung';
                } elseif ($today->gt($endDate)) {
                    if ($item->plan_end && $today->gt(Carbon::parse($item->plan_end))) {
                        $status = 'Terlambat';
                    } else {
                        $status = 'Selesai';
                    }
                }
            } elseif ($item->tanggal_mulai && !$item->tanggal_selesai) {
                $status = 'Sedang Berlangsung';
            }

            $key = $auditeeName . '|' . $jenisAudit;

            if (!isset($dashboardData[$key])) {
                $dashboardData[$key] = [
                    'auditee'                 => $auditeeName,
                    'jenis_audit'             => $jenisAudit,
                    'rencana_audit_mulai'     => $planningStart,
                    'rencana_audit_selesai'   => $planningFinish,
                    'realisasi_audit_mulai'   => $realisasiStart,
                    'realisasi_audit_selesai' => $realisasiFinish,
                    'status_realisasi'        => $status,
                    'status_approval'         => $item->status_approval ?? 'pending',
                    'schedule'                => array_fill_keys($months, []),
                ];
            }

            // Populate months with audit schedule
            if ($item->tanggal_mulai && $item->tanggal_selesai) {
                $startDate = Carbon::parse($item->tanggal_mulai);
                $endDate = Carbon::parse($item->tanggal_selesai);

                foreach ($months as $month) {
                    $monthNum = Carbon::parse($month)->month;
                    if (($startDate->month <= $monthNum && $startDate->year <= $endDate->year) &&
                        ($endDate->month >= $monthNum && $endDate->year >= $startDate->year)) {
                        $dashboardData[$key]['schedule'][$month][] = $item->id;
                    }
                }
            }
        }

        $dashboardData = array_values($dashboardData);

        return view('audit.dashboard-pelaksanaan-audit.index', compact('dashboardData', 'months'));
    }
}
