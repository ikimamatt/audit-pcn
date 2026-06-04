<?php

namespace App\Http\Controllers\Audit\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardPkptController extends Controller
{
    public function index(Request $request)
    {
        // Build Entry Meeting data via raw JOIN (eliminates N+1 and foreach loop)
        $emQuery = DB::table('entry_meeting as em')
            ->join('master_auditee as ma', 'em.auditee_id', '=', 'ma.id')
            ->leftJoin('program_kerja_audit as pka', 'em.program_kerja_audit_id', '=', 'pka.id')
            ->leftJoin('perencanaan_audit as pa', 'pka.perencanaan_audit_id', '=', 'pa.id')
            ->select(
                'em.id',
                'em.actual_meeting_date',
                'em.tanggal',
                'ma.nama_bidang',
                DB::raw('COALESCE(pa.jenis_audit, "Audit Operasional") as jenis_audit'),
                'pa.auditor',
                'pka.perencanaan_audit_id',
                DB::raw("(SELECT MIN(tanggal_mulai) FROM pka_milestone WHERE program_kerja_audit_id = pka.id) as plan_start"),
                DB::raw("(SELECT MAX(tanggal_selesai) FROM pka_milestone WHERE program_kerja_audit_id = pka.id) as plan_end"),
                DB::raw("(SELECT tanggal_mulai FROM pka_milestone WHERE program_kerja_audit_id = pka.id AND nama_milestone = 'Entry Meeting' LIMIT 1) as entry_plan_start"),
                DB::raw("(SELECT tanggal_selesai FROM pka_milestone WHERE program_kerja_audit_id = pka.id AND nama_milestone = 'Exit Meeting' LIMIT 1) as exit_plan_end")
            );

        // Filter by month
        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            $emQuery->where(function ($query) use ($selectedMonth) {
                $query->whereYear('em.tanggal', $selectedMonth->year)
                      ->whereMonth('em.tanggal', $selectedMonth->month);
            });
        }

        $entryMeetingRows = $emQuery->get();

        // Build Exit Meeting data via raw JOIN
        $exQuery = DB::table('exit_meeting_uploads as exm')
            ->join('master_auditee as ma', 'exm.auditee_id', '=', 'ma.id')
            ->select(
                'exm.id',
                'exm.tanggal_exit_meeting',
                'ma.nama_bidang'
            );

        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            $exQuery->where(function ($query) use ($selectedMonth) {
                $query->whereYear('exm.tanggal_exit_meeting', $selectedMonth->year)
                      ->whereMonth('exm.tanggal_exit_meeting', $selectedMonth->month);
            });
        }

        $exitMeetingRows = $exQuery->get();

        // Pre-count PKA per perencanaan_audit_id in 1 query (eliminates N+1)
        $perencanaanIds = $entryMeetingRows->pluck('perencanaan_audit_id')->filter()->unique()->toArray();
        $pkaCounts = [];
        if (!empty($perencanaanIds)) {
            $pkaCounts = DB::table('program_kerja_audit')
                ->whereIn('perencanaan_audit_id', $perencanaanIds)
                ->select('perencanaan_audit_id', DB::raw('COUNT(*) as cnt'))
                ->groupBy('perencanaan_audit_id')
                ->pluck('cnt', 'perencanaan_audit_id')
                ->toArray();
        }

        // Generate months
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create(null, $i, 1)->translatedFormat('M');
        }

        $dashboardData = [];

        // Process Entry Meeting data (no Eloquent loop, just simple iteration over raw result)
        foreach ($entryMeetingRows as $item) {
            $auditeeName = $this->buildAuditeeName($item);
            $key = 'entry_meeting_' . $item->id;

            $planningStart = $item->entry_plan_start ?? $item->plan_start;
            $planningEnd = $item->exit_plan_end ?? $item->plan_end;

            // Auditor count
            $jumlahAuditor = 1;
            if ($item->auditor) {
                $decoded = json_decode($item->auditor, true);
                $jumlahAuditor = is_array($decoded) ? count($decoded) : 1;
            }

            $dashboardData[$key] = [
                'auditee'                 => $auditeeName,
                'jenis_audit'             => $item->jenis_audit,
                'jumlah_auditor'          => $jumlahAuditor,
                'jumlah_pka'              => $pkaCounts[$item->perencanaan_audit_id] ?? 1,
                'rencana_audit_mulai'     => $planningStart ? Carbon::parse($planningStart)->format('d M Y') : '-',
                'rencana_audit_selesai'   => $planningEnd ? Carbon::parse($planningEnd)->format('d M Y') : '-',
                'realisasi_audit_mulai'   => $item->actual_meeting_date ? Carbon::parse($item->actual_meeting_date)->format('d M Y') : '-',
                'realisasi_audit_selesai' => '-',
                'status_realisasi'        => 'Sedang Berlangsung',
                'schedule'                => array_fill_keys($months, []),
                'source'                  => 'entry_meeting',
            ];

            if ($item->actual_meeting_date) {
                $monthName = Carbon::parse($item->actual_meeting_date)->translatedFormat('M');
                if (in_array($monthName, $months)) {
                    $dashboardData[$key]['schedule'][$monthName][] = $item->id;
                }
            }
        }

        // Process Exit Meeting data
        foreach ($exitMeetingRows as $item) {
            $auditeeName = $this->buildAuditeeName($item);
            $key = 'exit_meeting_' . $item->id;

            $dashboardData[$key] = [
                'auditee'                 => $auditeeName,
                'jenis_audit'             => 'Audit Operasional',
                'jumlah_auditor'          => 1,
                'jumlah_pka'              => 1,
                'rencana_audit_mulai'     => '-',
                'rencana_audit_selesai'   => '-',
                'realisasi_audit_mulai'   => $item->tanggal_exit_meeting ? Carbon::parse($item->tanggal_exit_meeting)->format('d M Y') : '-',
                'realisasi_audit_selesai' => $item->tanggal_exit_meeting ? Carbon::parse($item->tanggal_exit_meeting)->format('d M Y') : '-',
                'status_realisasi'        => 'Selesai',
                'schedule'                => array_fill_keys($months, []),
                'source'                  => 'exit_meeting',
            ];

            if ($item->tanggal_exit_meeting) {
                $monthName = Carbon::parse($item->tanggal_exit_meeting)->translatedFormat('M');
                if (in_array($monthName, $months)) {
                    $dashboardData[$key]['schedule'][$monthName][] = $item->id;
                }
            }
        }

        $dashboardData = array_values($dashboardData);

        // Keep these for backward compatibility with the view
        $entryMeetingData = $entryMeetingRows;
        $exitMeetingData = $exitMeetingRows;

        return view('audit.dashboard-pkpt.index', compact('dashboardData', 'months', 'entryMeetingData', 'exitMeetingData'));
    }

    /**
     * Build auditee display name from a raw DB row.
     */
    private function buildAuditeeName($row): string
    {
        return $row->nama_bidang ?? 'Unknown';
    }
}
