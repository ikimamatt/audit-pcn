<?php

namespace App\Http\Controllers\Audit\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardCacheService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardRencanaPkptController extends Controller
{
    public function index(Request $request)
    {
        $cache = app(DashboardCacheService::class);

        // Try cache first (for unfiltered requests)
        $cached = $cache->get('dashboard_rencana_pkpt');

        // Generate months for calendar view
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create(null, $i, 1)->translatedFormat('M');
        }

        $dashboardData = [];
        $pkaData = collect();

        if ($cached) {
            $dashboardData = $cached['dashboardData'];

            // Filter by month in PHP (already pre-computed, just filter the small result set)
            if ($request->filled('bulan')) {
                $selectedMonth = Carbon::parse($request->bulan);
                $dashboardData = collect($dashboardData)->filter(function ($item) use ($selectedMonth) {
                    if ($item['tanggal_pka_raw'] ?? false) {
                        $pkaMonth = Carbon::parse($item['tanggal_pka_raw']);
                        return $pkaMonth->year == $selectedMonth->year &&
                               $pkaMonth->month == $selectedMonth->month;
                    }
                    return true;
                })->values()->toArray();
            }

            // Pie chart data
            $collection = collect($dashboardData);
            $statusSelesai = $collection->where('status', 'Selesai')->count();
            $statusBerlangsung = $collection->where('status', 'Sedang Berlangsung')->count();
            $statusBelum = $collection->where('status', 'Belum Dimulai')->count();
            $statusTerlambat = $collection->where('status', 'Terlambat')->count();
        } else {
            // Cache miss: compute from cached service method
            $built = $cache->buildRencanaPkptData();

            $dashboardData = $built['dashboardData'];

            if ($request->filled('bulan')) {
                $selectedMonth = Carbon::parse($request->bulan);
                $dashboardData = collect($dashboardData)->filter(function ($item) use ($selectedMonth) {
                    if ($item['tanggal_pka_raw'] ?? false) {
                        $pkaMonth = Carbon::parse($item['tanggal_pka_raw']);
                        return $pkaMonth->year == $selectedMonth->year &&
                               $pkaMonth->month == $selectedMonth->month;
                    }
                    return true;
                })->values()->toArray();
            }

            $collection = collect($dashboardData);
            $statusSelesai = $collection->where('status', 'Selesai')->count();
            $statusBerlangsung = $collection->where('status', 'Sedang Berlangsung')->count();
            $statusBelum = $collection->where('status', 'Belum Dimulai')->count();
            $statusTerlambat = $collection->where('status', 'Terlambat')->count();
        }

        // Keep pkaData for backward compatibility with view
        $pkaData = collect($dashboardData);

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
