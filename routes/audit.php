<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Audit\PerencanaanAuditController;
use App\Http\Controllers\Audit\ToeAuditController;
use App\Http\Controllers\Audit\ToeEvaluasiController;
use App\Http\Controllers\Audit\EntryMeetingController;
use App\Http\Controllers\Audit\MonitoringTindakLanjutController;

// Audit Routes
Route::prefix('audit')->name('audit.')->group(function () {
    // Perencanaan Audit
    Route::resource('perencanaan', PerencanaanAuditController::class);
    Route::get('perencanaan/get-nomor-surat-tugas', [PerencanaanAuditController::class, 'getNomorSuratTugas'])->name('perencanaan.get-nomor-surat-tugas');
    Route::resource('pka', \App\Http\Controllers\Http\Controllers\Audit\ProgramKerjaAuditController::class);
    Route::post('pka/{pka}/dokumen/{dok}/approval', [\App\Http\Controllers\Http\Controllers\Audit\ProgramKerjaAuditController::class, 'approval'])->name('pka.approval');
    Route::resource('pkpt', \App\Http\Controllers\Http\Controllers\Audit\JadwalPkptAuditController::class);
    Route::post('pkpt/{pkpt}/approval', [\App\Http\Controllers\Http\Controllers\Audit\JadwalPkptAuditController::class, 'approval'])->name('pkpt.approval');
    Route::resource('walkthrough', \App\Http\Controllers\Audit\WalkthroughAuditController::class);
    Route::resource('exit-meeting', \App\Http\Controllers\Audit\ExitMeetingController::class)->except(['show']);
    Route::post('exit-meeting/{id}/approval', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'approval'])->name('exit-meeting.approval');
    Route::post('walkthrough/{walkthrough}/approval', [\App\Http\Controllers\Audit\WalkthroughAuditController::class, 'approval'])->name('walkthrough.approval');
    Route::resource('tod-bpm', \App\Http\Controllers\Audit\TodBpmAuditController::class);
    Route::post('tod-bpm/{tod_bpm}/approval', [\App\Http\Controllers\Audit\TodBpmAuditController::class, 'approval'])->name('tod-bpm.approval');
    Route::resource('tod-bpm-evaluasi', \App\Http\Controllers\Audit\TodBpmEvaluasiController::class);
    Route::get('tod-bpm-evaluasi-modal/{bpmId}', [\App\Http\Controllers\Audit\TodBpmEvaluasiController::class, 'modal'])->name('tod-bpm-evaluasi.modal');

    // TOE Audit
    Route::resource('toe', ToeAuditController::class);
    Route::post('toe/{id}/approval', [ToeAuditController::class, 'approval'])->name('toe.approval');

    // TOE Evaluasi
    Route::get('toe-evaluasi', [ToeEvaluasiController::class, 'index'])->name('toe-evaluasi.index');
    Route::post('toe-evaluasi', [ToeEvaluasiController::class, 'store'])->name('toe-evaluasi.store');
    Route::put('toe-evaluasi/{id}', [ToeEvaluasiController::class, 'update'])->name('toe-evaluasi.update');
    Route::delete('toe-evaluasi/{id}', [ToeEvaluasiController::class, 'destroy'])->name('toe-evaluasi.destroy');
    Route::get('toe-evaluasi-modal/{toeId}', [ToeEvaluasiController::class, 'modal'])->name('toe-evaluasi.modal');

    // Entry Meeting
    Route::resource('entry-meeting', EntryMeetingController::class);
    Route::post('entry-meeting/{id}/approval', [EntryMeetingController::class, 'approval'])->name('entry-meeting.approval');

    // Upload Exit Meeting
    Route::resource('upload/exit-meeting', \App\Http\Controllers\Audit\ExitMeetingUploadController::class)->names('upload.exit-meeting');
    Route::post('upload/exit-meeting/{id}/approval', [\App\Http\Controllers\Audit\ExitMeetingUploadController::class, 'approval'])->name('upload.exit-meeting.approval');

    // Upload LHA/LHK
    Route::resource('upload/lha-lhk', \App\Http\Controllers\Audit\LhaLhkUploadController::class)->names('upload.lha-lhk');
    Route::post('upload/lha-lhk/{id}/approval', [\App\Http\Controllers\Audit\LhaLhkUploadController::class, 'approval'])->name('upload.lha-lhk.approval');

    // Upload Nota Dinas
    Route::resource('upload/nota-dinas', \App\Http\Controllers\Audit\NotaDinasUploadController::class)->names('upload.nota-dinas');

    // Upload Dokumen Gabungan
    Route::resource('unggah-dokumen', \App\Http\Controllers\Audit\UnggahDokumenController::class)->names('unggah-dokumen');
    Route::post('unggah-dokumen/{id}/approve', [\App\Http\Controllers\Audit\UnggahDokumenController::class, 'approve'])->name('unggah-dokumen.approve');

    // Pelaporan Hasil Audit
    Route::resource('pelaporan-hasil-audit', \App\Http\Controllers\Audit\PelaporanHasilAuditController::class);
    Route::post('pelaporan-hasil-audit/{id}/approval', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'approval'])->name('pelaporan-hasil-audit.approval');
    Route::post('pelaporan-hasil-audit/generate-nomor-lhk', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'generateNomorLhk'])->name('pelaporan-hasil-audit.generate-nomor-lhk');
    Route::post('pelaporan-hasil-audit/generate-nomor-iss', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'generateNomorIss'])->name('pelaporan-hasil-audit.generate-nomor-iss');
    Route::get('pelaporan-hasil-audit/{id}/temuan', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'getTemuanData'])->name('pelaporan-hasil-audit.get-temuan');
    Route::get('pelaporan-hasil-audit/temuan/{id}', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'getTemuanById'])->name('pelaporan-hasil-audit.get-temuan-by-id');
    Route::put('pelaporan-hasil-audit/temuan/{id}', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'updateTemuan'])->name('pelaporan-hasil-audit.update-temuan');
    Route::get('pelaporan-hasil-audit/temuan-for-penutup', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'getAllTemuanForPenutup'])->name('pelaporan-hasil-audit.temuan-for-penutup');
    
    // Test route untuk debugging
    Route::get('test-temuan/{id}', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'getTemuanById'])->name('test.temuan');
    // Pelaporan Temuan (disabled - using new methods in PelaporanHasilAuditController)
    // Route::resource('pelaporan-temuan', \App\Http\Controllers\Audit\PelaporanTemuanController::class);
    // Route::post('pelaporan-temuan/{id}/approval', [\App\Http\Controllers\Audit\PelaporanTemuanController::class, 'approval'])->name('pelaporan-temuan.approval');

    // ISI LHA/LHK (disabled - view only)
    // Route::resource('isi-lha', \App\Http\Controllers\Audit\PelaporanIsiLhaController::class);
    // Route::get('isi-lha/get-nomor-iss/{id}', [\App\Http\Controllers\Audit\PelaporanIsiLhaController::class, 'getNomorIss'])->name('isi-lha.get-nomor-iss');
    // Route::post('isi-lha/{id}/approval', [\App\Http\Controllers\Audit\PelaporanIsiLhaController::class, 'approval'])->name('isi-lha.approval');

    // Penutup LHA/LHK Rekomendasi
    Route::get('penutup-lha-rekomendasi/select-nomor-surat-tugas', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'selectNomorSuratTugas'])->name('penutup-lha-rekomendasi.select-nomor-surat-tugas');
    Route::resource('penutup-lha-rekomendasi', \App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class);
    Route::post('penutup-lha-rekomendasi/{id}/approval', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'approval'])->name('penutup-lha-rekomendasi.approval');
    Route::get('penutup-lha-rekomendasi/get-iss-data', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'getIssData'])->name('penutup-lha-rekomendasi.get-iss-data');
    // Tindak lanjut routes
    Route::get('penutup-lha-rekomendasi/{rekomendasi}/tindak-lanjut', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'tindakLanjutForm'])->name('penutup-lha-rekomendasi.tindak-lanjut.form');
    Route::post('penutup-lha-rekomendasi/{rekomendasi}/tindak-lanjut', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'storeTindakLanjut'])->name('penutup-lha-rekomendasi.tindak-lanjut.store');
    Route::get('penutup-lha-tindak-lanjut/{id}/edit', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'editTindakLanjut'])->name('penutup-lha-tindak-lanjut.edit');
    Route::put('penutup-lha-tindak-lanjut/{id}', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'updateTindakLanjut'])->name('penutup-lha-tindak-lanjut.update');
    Route::delete('penutup-lha-tindak-lanjut/{id}', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'destroyTindakLanjut'])->name('penutup-lha-tindak-lanjut.destroy');

    // Dashboard PKPT
    Route::get('dashboard-pkpt', [\App\Http\Controllers\Audit\DashboardPkptController::class, 'index'])->name('dashboard-pkpt.index');
    
    // Dashboard Pelaksanaan Audit (disabled)
    // Route::get('dashboard-pelaksanaan-audit', [\App\Http\Controllers\Audit\DashboardPelaksanaanAuditController::class, 'index'])->name('dashboard-pelaksanaan-audit.index');
    
    // Realisasi Audit (disabled)
    // Route::get('realisasi-audit', [\App\Http\Controllers\Audit\RealisasiAuditController::class, 'index'])->name('realisasi-audit.index');
    // Route::get('realisasi-audit/{auditee}/{jenis_audit}', [\App\Http\Controllers\Audit\RealisasiAuditController::class, 'show'])->name('realisasi-audit.show');
    
    // Dashboard Monitoring Tindak Lanjut
    Route::get('monitoring-tindak-lanjut', [\App\Http\Controllers\Audit\MonitoringTindakLanjutController::class, 'index'])->name('monitoring-tindak-lanjut.index');
    
    // Pemantauan Hasil Audit
    Route::get('pemantauan', [\App\Http\Controllers\Audit\PemantauanAuditController::class, 'index'])->name('pemantauan.index');
    Route::get('pemantauan/{id}/edit', [\App\Http\Controllers\Audit\PemantauanAuditController::class, 'edit'])->name('pemantauan.edit');
    Route::put('pemantauan/{id}', [\App\Http\Controllers\Audit\PemantauanAuditController::class, 'update'])->name('pemantauan.update');
    Route::get('pemantauan/{id}/tindak-lanjut', [\App\Http\Controllers\Audit\PemantauanAuditController::class, 'tindakLanjutIndex'])->name('pemantauan.tindak-lanjut.index');
    Route::get('exit-meeting/pie', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'pie'])->name('exit-meeting.pie');
    Route::get('exit-meeting/chart', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'chart'])->name('exit-meeting.chart');
});

// Simple routes for exit meeting management
Route::prefix('exit-meeting')->name('exit-meeting.')->group(function () {
    // Route::get('/', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'index'])->name('index');
    Route::get('/', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'chart'])->name('index'); // Redirect to chart instead of index

    Route::get('/create', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'destroy'])->name('destroy');
});
