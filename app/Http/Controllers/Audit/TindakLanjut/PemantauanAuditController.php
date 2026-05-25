<?php

namespace App\Http\Controllers\Audit\TindakLanjut;

use App\Http\Controllers\Controller;
use App\Mail\ReminderRekomendasiMail;
use App\Models\EmailNotificationLog;
use App\Models\PenutupLhaRekomendasi;
use App\Models\Audit\PerencanaanAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Helpers\AuthHelper;

class PemantauanAuditController extends Controller
{
    public function selectNomorSuratTugas(Request $request)
    {
        $currentUserId = AuthHelper::getCurrentUserId();
        $canSeeAllData = AuthHelper::canSeeAllData();
        
        // Ambil semua perencanaan audit yang memiliki rekomendasi
        $query = PerencanaanAudit::whereHas('pelaporanHasilAudit.temuan.penutupLhaRekomendasi');
        
        // Jika user adalah AUDITEE, filter berdasarkan unit (area) mereka saja
        if (\App\Helpers\AuthHelper::isAuditee() && $currentUserId) {
            $userAreaId = \App\Helpers\AuthHelper::getUserAreaId();
            if ($userAreaId) {
                $query->where('area_id', $userAreaId);
            }
        }
        
        // Filter berdasarkan search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat_tugas', 'like', '%' . $search . '%')
                  ->orWhereHas('pelaporanHasilAudit', function($q2) use ($search) {
                      $q2->where('nomor_lha_lhk', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Filter berdasarkan jenis audit
        if ($request->filled('jenis_audit')) {
            $query->where('jenis_audit', $request->jenis_audit);
        }
        
        $perencanaanList = $query->with(['pelaporanHasilAudit'])->get();
        
        $nomorSuratTugasList = $perencanaanList
            ->map(function($perencanaan) {
                // Hitung rekomendasi secara langsung dari tabel PenutupLhaRekomendasi
                // agar akurat meskipun satu temuan punya >1 rekomendasi
                $rekomendasiQuery = PenutupLhaRekomendasi::whereHas('temuan.pelaporanHasilAudit', function($q) use ($perencanaan) {
                    $q->where('perencanaan_audit_id', $perencanaan->id);
                });
                
                $totalRekomendasi = $rekomendasiQuery->count();
                
                // Kumpulkan nomor LHA/LHK
                $nomorLhaLhkList = $perencanaan->pelaporanHasilAudit
                    ->pluck('nomor_lha_lhk')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();
                
                return [
                    'nomor_surat_tugas' => $perencanaan->nomor_surat_tugas,
                    'perencanaan_audit_id' => $perencanaan->id,
                    'jenis_audit' => $perencanaan->jenis_audit,
                    'nomor_lha_lhk' => implode(', ', $nomorLhaLhkList),
                    'count_rekomendasi' => $totalRekomendasi,
                ];
            })
            ->where('count_rekomendasi', '>', 0) // Hanya yang punya rekomendasi
            ->sortBy('nomor_surat_tugas')
            ->values();
        
        // Ambil daftar jenis audit untuk filter dropdown
        $jenisAuditQuery = PerencanaanAudit::whereHas('pelaporanHasilAudit.temuan.penutupLhaRekomendasi');
        
        // Filter dropdown jenis audit juga berdasarkan unit auditee
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAreaId = \App\Helpers\AuthHelper::getUserAreaId();
            if ($userAreaId) {
                $jenisAuditQuery->where('area_id', $userAreaId);
            }
        }
        
        $jenisAuditList = $jenisAuditQuery
            ->distinct()
            ->pluck('jenis_audit')
            ->sort()
            ->values();
        
        return view('audit.pemantauan.select-nomor-surat-tugas', compact('nomorSuratTugasList', 'jenisAuditList'));
    }

    public function index(Request $request)
    {
        // Jika tidak ada nomor_surat_tugas, redirect ke halaman pemilihan
        if (!$request->filled('nomor_surat_tugas')) {
            return redirect()->route('audit.pemantauan.select-nomor-surat-tugas');
        }
        
        $nomorSuratTugas = $request->get('nomor_surat_tugas');
        $currentUserId = AuthHelper::getCurrentUserId();
        $canSeeAllData = AuthHelper::canSeeAllData();
        
        // Load data with all necessary relationships
        $query = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'tindakLanjut',
            'picUsers'
        ]);
        
        // Filter berdasarkan nomor surat tugas
        if ($nomorSuratTugas) {
            $query->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($nomorSuratTugas) {
                $q->where('nomor_surat_tugas', $nomorSuratTugas);
            });
        }
        
        // Jika user adalah AUDITEE, filter berdasarkan unit (area) mereka
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAreaId = \App\Helpers\AuthHelper::getUserAreaId();
            if ($userAreaId) {
                $query->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($userAreaId) {
                    $q->where('area_id', $userAreaId);
                });
            }
        }
        
        // Apply month filter if provided
        if ($request->filled('bulan')) {
            [$year, $month] = explode('-', $request->bulan);
            $query->whereYear('target_waktu', $year)->whereMonth('target_waktu', $month);
        }
        
        $data = $query->get();
        
        // Ambil info perencanaan audit untuk ditampilkan
        $perencanaanAudit = null;
        if ($nomorSuratTugas) {
            $perencanaanAudit = PerencanaanAudit::where('nomor_surat_tugas', $nomorSuratTugas)->first();
        }
        
        $canSendReminder = \App\Helpers\AuthHelper::isSpiTeam() || \App\Helpers\AuthHelper::isSuperAdmin();

        return view('audit.pemantauan.index', compact('data', 'nomorSuratTugas', 'perencanaanAudit', 'canSendReminder'));
    }

    public function edit($id)
    {
        $currentUserId = AuthHelper::getCurrentUserId();
        $canSeeAllData = AuthHelper::canSeeAllData();
        
        $item = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'picUsers'
        ])->findOrFail($id);
        
        if (!\App\Helpers\AuthHelper::canModifyData()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit rekomendasi ini.');
        }
        
        return view('audit.pemantauan.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $currentUserId = AuthHelper::getCurrentUserId();
        $canSeeAllData = AuthHelper::canSeeAllData();
        
        $item = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'picUsers'
        ])->findOrFail($id);
        
        if (!\App\Helpers\AuthHelper::canModifyData()) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate rekomendasi ini.');
        }
        
        $request->validate([
            'rekomendasi' => 'required|string|max:5000',
            'rencana_aksi' => 'required|string|max:5000',
            'eviden_rekomendasi' => 'required|string|max:5000',
            'pic_rekomendasi' => 'required|string|max:500',
            'target_waktu' => 'required|date',
        ]);
        $item->update($request->only(['rekomendasi', 'rencana_aksi', 'eviden_rekomendasi', 'pic_rekomendasi', 'target_waktu']));
        
        // Ambil nomor surat tugas dari rekomendasi
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

    public function updateStatus(Request $request, $id)
    {
        $rekomendasi = PenutupLhaRekomendasi::findOrFail($id);
        
        $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|min:10',
        ], [
            'rejection_reason.required_if' => 'Alasan penolakan harus diisi jika Anda menolak tindak lanjut.',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

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

    /**
     * Kirim email pengingat ke semua PIC rekomendasi (manual trigger).
     * Mengirim ke: business_contact (auditee) + approval_1_spi + approval_2_spi
     */
    public function sendReminder(Request $request, $id)
    {
        $currentUser = Auth::user();

        // Hanya SPI yang boleh kirim reminder manual
        $namaAkses = optional(optional($currentUser)->akses)->nama_akses ?? '';
        if (! in_array($namaAkses, [
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

        // Ambil semua PIC yang memiliki email
        $pics = $rekomendasi->picUsers()->whereNotNull('email')->get();

        if ($pics->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada PIC dengan email terdaftar untuk rekomendasi ini.'
            ], 422);
        }

        $sentTo  = [];
        $failed  = [];

        foreach ($pics as $pic) {
            try {
                Mail::to($pic->email, $pic->nama)
                    ->send(new ReminderRekomendasiMail($rekomendasi, $pic, 'manual'));

                // Catat log berhasil
                EmailNotificationLog::create([
                    'penutup_lha_rekomendasi_id' => $rekomendasi->id,
                    'master_user_id'             => $pic->id,
                    'trigger_type'               => 'manual',
                    'sent_by'                    => $currentUser->id,
                    'status'                     => 'sent',
                    'sent_at'                    => now(),
                ]);

                $sentTo[] = $pic->nama;
            } catch (\Throwable $e) {
                // Catat log gagal
                EmailNotificationLog::create([
                    'penutup_lha_rekomendasi_id' => $rekomendasi->id,
                    'master_user_id'             => $pic->id,
                    'trigger_type'               => 'manual',
                    'sent_by'                    => $currentUser->id,
                    'status'                     => 'failed',
                    'error_message'              => $e->getMessage(),
                    'sent_at'                    => now(),
                ]);

                $failed[] = $pic->nama;
            }
        }

        // Update timestamp notifikasi terakhir
        $rekomendasi->update(['last_notified_at' => now()]);

        if (! empty($sentTo) && empty($failed)) {
            return response()->json([
                'success'  => true,
                'message'  => 'Email pengingat berhasil dikirim ke ' . count($sentTo) . ' PIC.',
                'sent_to'  => $sentTo,
            ]);
        } elseif (! empty($sentTo) && ! empty($failed)) {
            return response()->json([
                'success'  => true,
                'message'  => 'Sebagian email terkirim. Berhasil: ' . count($sentTo) . ', Gagal: ' . count($failed),
                'sent_to'  => $sentTo,
                'failed'   => $failed,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Semua email gagal terkirim. Silakan periksa konfigurasi mail.',
                'failed'  => $failed,
            ], 500);
        }
    }
} 