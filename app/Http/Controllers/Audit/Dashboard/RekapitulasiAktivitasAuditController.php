<?php

namespace App\Http\Controllers\Audit\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardCacheService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekapitulasiAktivitasAuditController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->filled('tahun') ? (int) $request->tahun : (int) date('Y');

        $cache = app(DashboardCacheService::class);
        $cacheKey = "rekapitulasi_{$selectedYear}";
        $cached = $cache->get($cacheKey);

        if (!$cached) {
            // Cache miss — build and store
            $cached = $cache->buildRekapitulasiData($selectedYear);
            $cache->put($cacheKey, $cached);
        }

        $pkaStatusData = $cached['pkaStatusData'];
        $aktivitasData = $cached['aktivitasData'];
        $bulananData   = $cached['bulananData'];
        $months        = $cached['months'];
        $approvalData  = $cached['approvalData'];
        $auditeeData   = collect($cached['auditeeData']);
        $totalSummary  = $cached['totalSummary'];

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
}
