<?php

namespace App\Http\Controllers\Audit;

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
    public function index(Request $request)
    {
        if (AuthHelper::isAuditee()) {
            abort(403, 'Auditee tidak memiliki akses ke halaman ini.');
        }

        $userId = Auth::id();
        $isSuperAdmin = AuthHelper::isSuperAdmin();

        // 1. Fetch PKAs
        $pkas = ProgramKerjaAudit::with(['perencanaanAudit.auditee', 'perencanaanAudit.ketuaTim', 'perencanaanAudit.koordinator'])
            ->whereIn('status_approval', ['pending', 'approved_level1'])
            ->get();

        // 2. Fetch Entry Meetings
        $entryMeetings = EntryMeeting::with(['programKerjaAudit.perencanaanAudit.auditee', 'programKerjaAudit.perencanaanAudit.ketuaTim', 'programKerjaAudit.perencanaanAudit.koordinator'])
            ->whereIn('status_approval', ['pending', 'approved_level1'])
            ->get();

        // 3. Fetch Walkthroughs
        $walkthroughs = WalkthroughAudit::with(['perencanaanAudit.auditee', 'perencanaanAudit.ketuaTim', 'perencanaanAudit.koordinator'])
            ->whereIn('status_approval', ['pending', 'approved_level1'])
            ->get();

        // 4. Fetch TOD BPM
        $todBpms = TodBpmAudit::with(['perencanaanAudit.auditee', 'perencanaanAudit.ketuaTim', 'perencanaanAudit.koordinator'])
            ->whereIn('status_approval', ['pending', 'approved_level1'])
            ->get();

        // 5. Fetch TOEs
        $toes = ToeAudit::with(['perencanaanAudit.auditee', 'perencanaanAudit.ketuaTim', 'perencanaanAudit.koordinator'])
            ->whereIn('status_approval', ['pending', 'approved_level1'])
            ->get();

        // 6. Fetch Exit Meetings (RealisasiAudit)
        $exitMeetings = RealisasiAudit::with(['perencanaanAudit.auditee', 'perencanaanAudit.ketuaTim', 'perencanaanAudit.koordinator'])
            ->whereIn('status_approval', ['pending', 'approved_level1'])
            ->get();

        // 7. Fetch Pelaporan Hasil Audit (LHA/LHK)
        $pelaporans = PelaporanHasilAudit::with(['perencanaanAudit.auditee', 'perencanaanAudit.ketuaTim', 'perencanaanAudit.koordinator'])
            ->whereIn('status_approval', ['pending', 'approved_level1'])
            ->get();

        // 8. Fetch Penutup LHA Rekomendasi
        $penutups = PenutupLhaRekomendasi::with(['temuan.pelaporanHasilAudit.perencanaanAudit.auditee', 'temuan.pelaporanHasilAudit.perencanaanAudit.ketuaTim', 'temuan.pelaporanHasilAudit.perencanaanAudit.koordinator'])
            ->whereIn('status_approval', ['pending', 'approved_level1'])
            ->get();

        $allPendingItems = collect();

        // Helper function to process item mapping
        $mapItem = function($item, $modelType, $docName, $perencanaan, $title, $detailUrl) use ($userId, $isSuperAdmin) {
            if (!$perencanaan) return null;

            $status = $item->status_approval ?? 'pending';
            
            // Check if user is allowed to approve/reject
            $canApprove = false;
            $canReject = false;
            
            $isKetua = (int)($perencanaan->ketua_tim_id ?? 0) === $userId;
            $isKoordinator = (int)($perencanaan->koordinator_id ?? 0) === $userId;

            if ($status === 'pending') {
                $canApprove = ($isKetua || $isSuperAdmin);
                $canReject = ($isKetua || $isSuperAdmin);
            } elseif ($status === 'approved_level1') {
                $canApprove = ($isKoordinator || $isSuperAdmin);
                $canReject = ($isKoordinator || $isSuperAdmin);
            }

            // If the user cannot approve/reject and is not super admin, we filter it out
            if (!$canApprove && !$isSuperAdmin) {
                return null;
            }

            return [
                'id' => $item->id,
                'model_type' => $modelType,
                'document_name' => $docName,
                'nomor_surat_tugas' => $perencanaan->nomor_surat_tugas ?? '-',
                'auditee_name' => $perencanaan->auditee->divisi ?? '-',
                'title' => $title,
                'status_approval' => $status,
                'approval_level' => $status === 'pending' ? 'Level 1 (Ketua Tim)' : 'Level 2 (Koordinator)',
                'date' => $item->updated_at ?? $item->created_at ?? now(),
                'detail_url' => $detailUrl,
                'can_approve' => $canApprove,
                'can_reject' => $canReject,
            ];
        };

        // Populate PKA
        foreach ($pkas as $item) {
            $mapped = $mapItem(
                $item, 
                'pka', 
                'Program Kerja Audit (PKA)', 
                $item->perencanaanAudit, 
                $item->judul_pka ?? 'Program Kerja Audit',
                route('audit.pka.show', $item->id)
            );
            if ($mapped) $allPendingItems->push($mapped);
        }

        // Populate Entry Meeting
        foreach ($entryMeetings as $item) {
            $perencanaan = $item->programKerjaAudit->perencanaanAudit ?? null;
            $mapped = $mapItem(
                $item, 
                'entry_meeting', 
                'Entry Meeting', 
                $perencanaan, 
                'Dokumen Entry Meeting',
                route('audit.entry-meeting.index', ['id' => $item->id])
            );
            if ($mapped) $allPendingItems->push($mapped);
        }

        // Populate Walkthrough
        foreach ($walkthroughs as $item) {
            $mapped = $mapItem(
                $item, 
                'walkthrough', 
                'Walkthrough Audit', 
                $item->perencanaanAudit, 
                'Walkthrough: ' . ($item->auditee_nama ?? 'Hasil Walkthrough'),
                route('audit.walkthrough.index', ['id' => $item->id])
            );
            if ($mapped) $allPendingItems->push($mapped);
        }

        // Populate TOD BPM
        foreach ($todBpms as $item) {
            $mapped = $mapItem(
                $item, 
                'tod_bpm', 
                'TOD BPM Audit', 
                $item->perencanaanAudit, 
                'TOD BPM: ' . ($item->auditee_nama ?? 'Hasil TOD BPM'),
                route('audit.tod-bpm.index', ['id' => $item->id])
            );
            if ($mapped) $allPendingItems->push($mapped);
        }

        // Populate TOE
        foreach ($toes as $item) {
            $mapped = $mapItem(
                $item, 
                'toe', 
                'TOE Audit', 
                $item->perencanaanAudit, 
                'TOE: ' . ($item->auditee_nama ?? 'Hasil TOE'),
                route('audit.toe.index', ['id' => $item->id])
            );
            if ($mapped) $allPendingItems->push($mapped);
        }

        // Populate Exit Meeting
        foreach ($exitMeetings as $item) {
            $mapped = $mapItem(
                $item, 
                'exit_meeting', 
                'Exit Meeting', 
                $item->perencanaanAudit, 
                'Exit Meeting: ' . ($item->perencanaanAudit->auditee->divisi ?? 'Hasil Exit Meeting'),
                route('audit.exit-meeting.index', ['id' => $item->id])
            );
            if ($mapped) $allPendingItems->push($mapped);
        }

        // Populate Pelaporan Hasil Audit (Judul LHA/LHK)
        foreach ($pelaporans as $item) {
            $mapped = $mapItem(
                $item, 
                'pelaporan_hasil_audit', 
                'Pelaporan Hasil Audit (LHA/LHK)', 
                $item->perencanaanAudit, 
                'Nomor LHA/LHK: ' . ($item->nomor_lha_lhk ?? 'LHA/LHK'),
                route('audit.pelaporan-hasil-audit.show', $item->id)
            );
            if ($mapped) $allPendingItems->push($mapped);
        }

        // Populate Penutup LHA Rekomendasi
        foreach ($penutups as $item) {
            $perencanaan = $item->temuan->pelaporanHasilAudit->perencanaanAudit ?? null;
            $rekomendasi = $item->rekomendasi ?? '-';
            $truncatedRekomendasi = strlen($rekomendasi) > 60 ? substr($rekomendasi, 0, 60) . '...' : $rekomendasi;
            $mapped = $mapItem(
                $item, 
                'penutup_lha_rekomendasi', 
                'Penutup LHA Rekomendasi', 
                $perencanaan, 
                'Rekomendasi: ' . $truncatedRekomendasi,
                route('audit.penutup-lha-rekomendasi.show', $item->id)
            );
            if ($mapped) $allPendingItems->push($mapped);
        }

        // Sort items by date descending (most recent first)
        $allPendingItems = $allPendingItems->sortByDesc('date')->values();

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

        $result = ApprovalHelper::processApproval($item, $action, $reason);

        if ($result['success']) {
            $item->refresh();

            // Custom post-processing logic (matching what is done in original controllers)
            if ($modelType === 'pelaporan_hasil_audit') {
                if ($action === 'approve' && $item->status_approval === 'approved') {
                    if ($item->temuan && $item->temuan->count() > 0) {
                        foreach ($item->temuan as $temuan) {
                            $temuan->update([
                                'status_approval' => 'approved',
                                'approved_by' => auth()->id(),
                                'approved_at' => now()
                            ]);
                        }
                    }
                } elseif ($action === 'reject' && $item->status_approval === 'rejected') {
                    if ($item->temuan && $item->temuan->count() > 0) {
                        foreach ($item->temuan as $temuan) {
                            $temuan->update([
                                'status_approval' => 'rejected',
                                'approved_by' => auth()->id(),
                                'approved_at' => now()
                            ]);
                        }
                    }
                }
            } elseif ($modelType === 'exit_meeting') {
                if ($action === 'approve' && $item->status_approval === 'approved') {
                    $item->status = 'selesai';
                    if (!$item->tanggal_selesai) {
                        $item->tanggal_selesai = now()->toDateString();
                    }
                    $item->save();
                } elseif ($action === 'reject') {
                    if ($item->tanggal_mulai && $item->tanggal_selesai) {
                        $item->status = 'selesai';
                    } elseif ($item->tanggal_mulai && !$item->tanggal_selesai) {
                        $item->status = 'on progress';
                    } else {
                        $item->status = 'belum';
                    }
                    $item->save();
                }
            }

            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
