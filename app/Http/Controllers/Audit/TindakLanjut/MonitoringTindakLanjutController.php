<?php

namespace App\Http\Controllers\Audit\TindakLanjut;

use App\Http\Controllers\Controller;
use App\Services\Audit\MonitoringService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MonitoringTindakLanjutController extends Controller
{
    protected $monitoringService;

    public function __construct(MonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    public function index(Request $request)
    {
        $selectedYear = $request->input('year', Carbon::now()->year);
        
        $userAreaId = null;
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAreaId = \App\Helpers\AuthHelper::getUserAreaId();
        }

        $data = $this->monitoringService->getMonitoringData($selectedYear, $userAreaId);

        return view('audit.monitoring-tindak-lanjut.index', $data);
    }
}
