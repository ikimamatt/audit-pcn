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
        
        $userAuditeeId = null;
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAuditeeId = \App\Helpers\AuthHelper::getUserAuditeeId();
        }

        $data = $this->monitoringService->getMonitoringData($selectedYear, $userAuditeeId);

        return view('audit.monitoring-tindak-lanjut.index', $data);
    }
}
