<?php

namespace App\Http\Controllers\Audit\PelaporanAudit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AuthHelper;
use App\Helpers\ApprovalHelper;

// Import all required models
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\EntryMeeting;
use App\Models\WalkthroughAudit;
use App\Models\TodBpmAudit;
use App\Models\ToeAudit;
use App\Models\RealisasiAudit;
use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\PenutupLhaRekomendasi;

class PersetujuanController extends Controller
{
    protected $persetujuanService;
    protected $pelaporanService;
    protected $exitMeetingService;

    public function __construct(
        \App\Services\Audit\PersetujuanService $persetujuanService,
        \App\Services\Audit\PelaporanHasilAuditService $pelaporanService,
        \App\Services\Audit\ExitMeetingService $exitMeetingService
    ) {
        $this->persetujuanService = $persetujuanService;
        $this->pelaporanService = $pelaporanService;
        $this->exitMeetingService = $exitMeetingService;
    }

    public function index(Request $request)
    {
        if (AuthHelper::isAuditee()) {
            abort(403, 'Auditee tidak memiliki akses ke halaman ini.');
        }

        $userId = Auth::id();
        $isSuperAdmin = AuthHelper::isSuperAdmin();

        $allPendingItems = $this->persetujuanService->getPendingItems($userId, $isSuperAdmin);

        return view('audit.persetujuan.index', compact('allPendingItems'));
    }

    public function proses(Request $request)
    {
        if (AuthHelper::isAuditee()) {
            abort(403);
        }

        $request->validate([
            'model_type' => 'required|string',
            'id' => 'required|integer',
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|min:10',
        ], [
            'rejection_reason.required_if' => 'Alasan penolakan harus diisi jika Anda menolak dokumen.',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

        $modelType = $request->input('model_type');
        $id = $request->input('id');
        $action = $request->input('action');
        $reason = $request->input('rejection_reason');

        $modelClass = match($modelType) {
            'pka' => ProgramKerjaAudit::class,
            'entry_meeting' => EntryMeeting::class,
            'walkthrough' => WalkthroughAudit::class,
            'tod_bpm' => TodBpmAudit::class,
            'toe' => ToeAudit::class,
            'exit_meeting' => RealisasiAudit::class,
            'pelaporan_hasil_audit' => PelaporanHasilAudit::class,
            'penutup_lha_rekomendasi' => PenutupLhaRekomendasi::class,
            default => null
        };

        if (!$modelClass) {
            return back()->with('error', 'Tipe dokumen tidak valid.');
        }

        $item = $modelClass::findOrFail($id);

        if ($modelType === 'pelaporan_hasil_audit') {
            $result = $this->pelaporanService->approve($item, $action, $reason);
        } elseif ($modelType === 'exit_meeting') {
            $result = $this->exitMeetingService->approve($item, $action, $reason);
        } else {
            $result = ApprovalHelper::processApproval($item, $action, $reason);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
