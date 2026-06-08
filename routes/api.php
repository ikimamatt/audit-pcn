<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API Controllers
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\PerencanaanAuditApiController;
use App\Http\Controllers\Api\PkptApiController;
use App\Http\Controllers\Api\ProgramKerjaAuditApiController;
use App\Http\Controllers\Api\EntryMeetingApiController;
use App\Http\Controllers\Api\ExitMeetingApiController;
use App\Http\Controllers\Api\WalkthroughApiController;
use App\Http\Controllers\Api\TodBpmApiController;
use App\Http\Controllers\Api\ToeApiController;
use App\Http\Controllers\Api\PelaporanApiController;
use App\Http\Controllers\Api\PenutupLhaApiController;
use App\Http\Controllers\Api\TindakLanjutApiController;
use App\Http\Controllers\Api\MasterDataApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes untuk Sanctum (default Laravel).
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| ERP Integration API Routes
|--------------------------------------------------------------------------
|
| Semua route dilindungi middleware 'erp.header.validate' yang memvalidasi
| token ERP (HMAC-SHA256) dan melakukan NIP matching ke master_user lokal.
|
| Base URL: /api/audit/...
|
*/

Route::middleware(['erp.header.validate'])->prefix('audit')->name('api.audit.')->group(function () {

    // ── Health Check ─────────────────────────────────────────────
    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'message' => 'Audit PCN Service is running.',
            'timestamp' => now()->toIso8601String(),
        ]);
    })->name('health');

    // ── Dashboard ────────────────────────────────────────────────
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/analitik', [DashboardApiController::class, 'analitik'])->name('analitik');
        Route::get('/aging-detail', [DashboardApiController::class, 'agingDetail'])->name('aging-detail');
        Route::get('/pkpt', [DashboardApiController::class, 'pkpt'])->name('pkpt');
        Route::get('/rekapitulasi', [DashboardApiController::class, 'rekapitulasi'])->name('rekapitulasi');
    });

    // ── Perencanaan Audit ────────────────────────────────────────
    Route::prefix('perencanaan')->name('perencanaan.')->group(function () {
        Route::get('/', [PerencanaanAuditApiController::class, 'index'])->name('index');
        Route::get('/form-data', [PerencanaanAuditApiController::class, 'formData'])->name('form-data');
        Route::get('/nomor-surat-tugas', [PerencanaanAuditApiController::class, 'getNomorSuratTugas'])->name('nomor-surat-tugas');
        Route::post('/', [PerencanaanAuditApiController::class, 'store'])->name('store');
        Route::get('/{id}', [PerencanaanAuditApiController::class, 'show'])->name('show');
        Route::put('/{id}', [PerencanaanAuditApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [PerencanaanAuditApiController::class, 'destroy'])->name('destroy');
    });

    // ── Jadwal PKPT ──────────────────────────────────────────────
    Route::apiResource('pkpt', PkptApiController::class);

    // ── Program Kerja Audit (PKA) ────────────────────────────────
    Route::prefix('pka')->name('pka.')->group(function () {
        Route::get('/', [ProgramKerjaAuditApiController::class, 'index'])->name('index');
        Route::post('/', [ProgramKerjaAuditApiController::class, 'store'])->name('store');
        Route::get('/{id}', [ProgramKerjaAuditApiController::class, 'show'])->name('show');
        Route::put('/{id}', [ProgramKerjaAuditApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProgramKerjaAuditApiController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/check-relations', [ProgramKerjaAuditApiController::class, 'checkRelations'])->name('check-relations');
        Route::get('/hierarki-flat/{perencanaanId}', [ProgramKerjaAuditApiController::class, 'getHierarkiFlat'])->name('hierarki-flat');
        Route::post('/{pkaId}/dokumen/{dokId}/approval', [ProgramKerjaAuditApiController::class, 'approvalDokumen'])->name('approval-dokumen');
        Route::post('/{id}/approval-main', [ProgramKerjaAuditApiController::class, 'approvalMain'])->name('approval-main');
    });

    // ── Walkthrough ──────────────────────────────────────────────
    Route::apiResource('walkthrough', WalkthroughApiController::class);
    Route::post('walkthrough/{id}/approval', [WalkthroughApiController::class, 'approval'])->name('walkthrough.approval');

    // ── TOD BPM ──────────────────────────────────────────────────
    Route::apiResource('tod-bpm', TodBpmApiController::class);
    Route::post('tod-bpm/{id}/approval', [TodBpmApiController::class, 'approval'])->name('tod-bpm.approval');
    Route::get('tod-bpm/{bpmId}/evaluasi', [TodBpmApiController::class, 'evaluasiIndex'])->name('tod-bpm.evaluasi.index');
    Route::post('tod-bpm-evaluasi', [TodBpmApiController::class, 'evaluasiStore'])->name('tod-bpm.evaluasi.store');
    Route::put('tod-bpm-evaluasi/{id}', [TodBpmApiController::class, 'evaluasiUpdate'])->name('tod-bpm.evaluasi.update');
    Route::delete('tod-bpm-evaluasi/{id}', [TodBpmApiController::class, 'evaluasiDestroy'])->name('tod-bpm.evaluasi.destroy');

    // ── TOE ──────────────────────────────────────────────────────
    Route::apiResource('toe', ToeApiController::class);
    Route::post('toe/{id}/approval', [ToeApiController::class, 'approval'])->name('toe.approval');
    Route::get('toe/{toeId}/evaluasi', [ToeApiController::class, 'evaluasiIndex'])->name('toe.evaluasi.index');
    Route::post('toe-evaluasi', [ToeApiController::class, 'evaluasiStore'])->name('toe.evaluasi.store');
    Route::put('toe-evaluasi/{id}', [ToeApiController::class, 'evaluasiUpdate'])->name('toe.evaluasi.update');
    Route::delete('toe-evaluasi/{id}', [ToeApiController::class, 'evaluasiDestroy'])->name('toe.evaluasi.destroy');

    // ── Entry Meeting ────────────────────────────────────────────
    Route::apiResource('entry-meeting', EntryMeetingApiController::class);
    Route::post('entry-meeting/{id}/approval', [EntryMeetingApiController::class, 'approval'])->name('entry-meeting.approval');

    // ── Exit Meeting ─────────────────────────────────────────────
    Route::apiResource('exit-meeting', ExitMeetingApiController::class);
    Route::post('exit-meeting/{id}/approval', [ExitMeetingApiController::class, 'approval'])->name('exit-meeting.approval');

    // ── Pelaporan Hasil Audit ────────────────────────────────────
    Route::prefix('pelaporan-hasil-audit')->name('pelaporan.')->group(function () {
        Route::get('/', [PelaporanApiController::class, 'index'])->name('index');
        Route::post('/', [PelaporanApiController::class, 'store'])->name('store');
        Route::get('/temuan-for-penutup', [PelaporanApiController::class, 'getAllTemuanForPenutup'])->name('temuan-for-penutup');
        Route::get('/{id}', [PelaporanApiController::class, 'show'])->name('show');
        Route::put('/{id}', [PelaporanApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [PelaporanApiController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/approval', [PelaporanApiController::class, 'approval'])->name('approval');
        Route::get('/{id}/temuan', [PelaporanApiController::class, 'getTemuan'])->name('temuan');
        Route::get('/temuan/{id}', [PelaporanApiController::class, 'getTemuanById'])->name('temuan-detail');
        Route::put('/temuan/{id}', [PelaporanApiController::class, 'updateTemuan'])->name('temuan-update');
        Route::post('/generate-nomor-lha-lhk', [PelaporanApiController::class, 'generateNomorLhaLhk'])->name('generate-nomor-lha-lhk');
        Route::post('/generate-nomor-iss', [PelaporanApiController::class, 'generateNomorIss'])->name('generate-nomor-iss');
    });

    // ── Penutup LHA Rekomendasi ──────────────────────────────────
    Route::prefix('penutup-lha')->name('penutup-lha.')->group(function () {
        Route::get('/', [PenutupLhaApiController::class, 'index'])->name('index');
        Route::post('/', [PenutupLhaApiController::class, 'store'])->name('store');
        Route::get('/{id}', [PenutupLhaApiController::class, 'show'])->name('show');
        Route::put('/{id}', [PenutupLhaApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [PenutupLhaApiController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/approval', [PenutupLhaApiController::class, 'approval'])->name('approval');
        Route::get('/{rekomendasiId}/tindak-lanjut', [PenutupLhaApiController::class, 'tindakLanjutIndex'])->name('tindak-lanjut.index');
        Route::post('/{rekomendasiId}/tindak-lanjut', [PenutupLhaApiController::class, 'tindakLanjutStore'])->name('tindak-lanjut.store');
    });

    // ── Tindak Lanjut & Pemantauan ───────────────────────────────
    Route::prefix('tindak-lanjut')->name('tindak-lanjut.')->group(function () {
        Route::get('/select-nomor-surat-tugas', [TindakLanjutApiController::class, 'selectNomorSuratTugas'])->name('select-nst');
        Route::get('/pemantauan', [TindakLanjutApiController::class, 'pemantauanIndex'])->name('pemantauan');
        Route::get('/pemantauan/{id}', [TindakLanjutApiController::class, 'tindakLanjutDetail'])->name('pemantauan-detail');
        Route::put('/pemantauan/{id}', [TindakLanjutApiController::class, 'editPemantauan'])->name('pemantauan-edit');
        Route::post('/pemantauan/{id}/status', [TindakLanjutApiController::class, 'updateStatus'])->name('update-status');
        Route::get('/monitoring', [TindakLanjutApiController::class, 'monitoringIndex'])->name('monitoring');
        Route::get('/progress', [TindakLanjutApiController::class, 'progressIndex'])->name('progress');
    });

    // ── Persetujuan ──────────────────────────────────────────────
    Route::get('/persetujuan', [TindakLanjutApiController::class, 'persetujuanIndex'])->name('persetujuan.index');
    Route::post('/persetujuan', [TindakLanjutApiController::class, 'persetujuanProses'])->name('persetujuan.proses');

    // ── Master Data (Read Only) ──────────────────────────────────
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/auditee', [MasterDataApiController::class, 'auditee'])->name('auditee');
        Route::get('/area', [MasterDataApiController::class, 'area'])->name('area');
        Route::get('/jenis-audit', [MasterDataApiController::class, 'jenisAudit'])->name('jenis-audit');
        Route::get('/kode-risk', [MasterDataApiController::class, 'kodeRisk'])->name('kode-risk');
        Route::get('/kode-aoi', [MasterDataApiController::class, 'kodeAoi'])->name('kode-aoi');
        Route::get('/user', [MasterDataApiController::class, 'user'])->name('user');
        Route::get('/region', [MasterDataApiController::class, 'region'])->name('region');
        Route::get('/sub-bidang', [MasterDataApiController::class, 'subBidang'])->name('sub-bidang');
    });
});
