<?php

namespace App\Http\Controllers\Audit\TindakLanjut;

use App\Http\Controllers\Controller;
use App\Services\Audit\MonitoringService;
use Illuminate\Http\Request;

class ProgressTindakLanjutController extends Controller
{
    protected $monitoringService;

    public function __construct(MonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    public function index(Request $request)
    {
        $selectedYear = $request->filled('tahun') ? $request->tahun : date('Y');
        $selectedStatus = $request->filled('status') ? $request->status : 'all';
        $selectedAuditee = $request->filled('auditee_id') ? $request->auditee_id : null;
        
        $userAreaId = null;
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAreaId = \App\Helpers\AuthHelper::getUserAreaId();
        }

        $data = $this->monitoringService->getProgressData(
            $selectedYear,
            $selectedStatus,
            $selectedAuditee,
            $userAreaId
        );

        return view('audit.progress-tindak-lanjut.index', $data);
    }
}
