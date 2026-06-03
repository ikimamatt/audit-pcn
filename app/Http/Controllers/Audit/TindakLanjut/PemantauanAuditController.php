<?php

namespace App\Http\Controllers\Audit\TindakLanjut;

use App\Http\Controllers\Controller;
use App\Mail\ReminderRekomendasiMail;
use App\Models\EmailNotificationLog;
use App\Models\PenutupLhaRekomendasi;
use App\Models\Audit\PerencanaanAudit;
use Illuminate\Http\Request;
use App\Http\Requests\Audit\TindakLanjut\UpdatePemantauanRekomendasiRequest;
use App\Http\Requests\Audit\PelaporanAudit\ApprovalRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Helpers\AuthHelper;
use App\Helpers\QueryHelper;

class PemantauanAuditController extends Controller
{
    protected $monitoringService;

    public function __construct(\App\Services\Audit\MonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    public function selectNomorSuratTugas(Request $request)
    {
        $userAreaId = null;
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAreaId = \App\Helpers\AuthHelper::getUserAreaId();
        }

        $result = $this->monitoringService->getSelectNomorSuratTugasList(
            $userAreaId,
            $request->input('search'),
            $request->input('jenis_audit')
        );

        $nomorSuratTugasList = $result['nomorSuratTugasList'];
        $jenisAuditList = $result['jenisAuditList'];

        return view('audit.pemantauan.select-nomor-surat-tugas', compact('nomorSuratTugasList', 'jenisAuditList'));
    }

    public function index(Request $request)
    {
        if (!$request->filled('nomor_surat_tugas')) {
            return redirect()->route('audit.pemantauan.select-nomor-surat-tugas');
        }

        $nomorSuratTugas = $request->get('nomor_surat_tugas');
        $userAreaId = null;
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAreaId = \App\Helpers\AuthHelper::getUserAreaId();
        }

        $result = $this->monitoringService->getPemantauanData(
            $nomorSuratTugas,
            $userAreaId,
            $request->input('bulan')
        );

        $data = $result['data'];
        $perencanaanAudit = $result['perencanaanAudit'];
        $canSendReminder = \App\Helpers\AuthHelper::isSpiTeam() || \App\Helpers\AuthHelper::isSuperAdmin();

        return view('audit.pemantauan.index', compact('data', 'nomorSuratTugas', 'perencanaanAudit', 'canSendReminder'));
    }

    public function edit($id)
    {
        $item = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'picUsers'
        ])->findOrFail($id);
        
        if (!\App\Helpers\AuthHelper::canModifyData()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit rekomendasi ini.');
        }
        
        return view('audit.pemantauan.edit', compact('item'));
    }

    public function update(UpdatePemantauanRekomendasiRequest $request, $id)
    {
        $item = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'picUsers'
        ])->findOrFail($id);
        
        if (!\App\Helpers\AuthHelper::canModifyData()) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate rekomendasi ini.');
        }
        
        $item->update($request->only(['rekomendasi', 'rencana_aksi', 'eviden_rekomendasi', 'pic_rekomendasi', 'target_waktu']));
        
        $nomorSuratTugas = null;
        if ($item->temuan && $item->temuan->pelaporanHasilAudit && $item->temuan->pelaporanHasilAudit->perencanaanAudit) {
            $nomorSuratTugas = $item->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas;
        }
        
        return redirect()->route('audit.pemantauan.index', ['nomor_surat_tugas' => $nomorSuratTugas])->with('success', 'Rekomendasi berhasil diupdate!');
    }

    public function tindakLanjutIndex($id)
    {
        $rekomendasi = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee'
        ])->findOrFail($id);
        $tindakLanjut = $rekomendasi->tindakLanjut()->orderBy('created_at', 'desc')->first();
        return view('audit.pemantauan.tindak-lanjut-index', compact('rekomendasi', 'tindakLanjut'));
    }

    public function updateStatus(ApprovalRequest $request, $id)
    {
        $rekomendasi = PenutupLhaRekomendasi::findOrFail($id);

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $rekomendasi,
            $request->action,
            $request->rejection_reason
        );

        if ($result['success']) {
            $rekomendasi->refresh();
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'new_status' => $rekomendasi->status_tindak_lanjut,
                'status_approval' => $rekomendasi->status_approval,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 403);
    }

    public function sendReminder(Request $request, $id)
    {
        $currentUser = Auth::user();

        // Hanya SPI yang boleh kirim reminder manual
        $namaAkses = optional(optional($currentUser)->akses)->nama_akses ?? '';
        if (!in_array($namaAkses, [
            'AUDITOR', 'Auditor',
            'ASMAN SPI',
            'KSPI',
            'SUPERADMIN', 'Superadmin', 'superadmin',
            'SUPER ADMIN', 'Super Admin',
        ])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengirim pengingat.'
            ], 403);
        }

        $rekomendasi = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'picUsers',
        ])->findOrFail($id);

        $result = $this->monitoringService->sendReminder($rekomendasi, $currentUser);

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, isset($result['failed']) ? 500 : 422);
    }
}
 