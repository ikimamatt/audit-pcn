<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Audit\PerencanaanAuditController;
use App\Http\Controllers\Audit\ToeAuditController;
use App\Http\Controllers\Audit\ToeEvaluasiController;
use App\Http\Controllers\Audit\EntryMeetingController;
use App\Http\Controllers\Audit\MonitoringTindakLanjutController;

// Audit Routes
Route::prefix('audit')->name('audit.')->group(function () {

    // =========================================================
    // HALAMAN AUDITEE-ACCESSIBLE (VIEW ONLY untuk AUDITEE)
    // =========================================================
    Route::middleware(['auth', 'role:KSPI,ASMAN SPI,AUDITOR,AUDITEE,SUPER ADMIN,VIEW BOD'])->group(function () {
        // Pelaporan Hasil Audit — AUDITEE hanya index + show (filter di controller)
        Route::get('pelaporan-hasil-audit', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'index'])->name('pelaporan-hasil-audit.index');
        Route::get('pelaporan-hasil-audit/{id}', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'show'])->name('pelaporan-hasil-audit.show');
    });

    // Pemantauan Hasil Audit (AUDITEE + VIEW BOD bisa akses, filter di controller)
    Route::middleware(['auth', 'role:KSPI,ASMAN SPI,AUDITOR,AUDITEE,SUPER ADMIN,VIEW BOD'])->group(function () {
        Route::get('pemantauan/select-nomor-surat-tugas', [\App\Http\Controllers\Audit\PemantauanAuditController::class, 'selectNomorSuratTugas'])->name('pemantauan.select-nomor-surat-tugas');
        Route::get('pemantauan', [\App\Http\Controllers\Audit\PemantauanAuditController::class, 'index'])->name('pemantauan.index');
        Route::get('pemantauan/{id}/tindak-lanjut', [\App\Http\Controllers\Audit\PemantauanAuditController::class, 'tindakLanjutIndex'])->name('pemantauan.tindak-lanjut.index');
        
        // Tindak Lanjut Form untuk PIC Business Contact (AUDITEE)
        Route::get('penutup-lha-rekomendasi/{rekomendasi}/tindak-lanjut', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'tindakLanjutForm'])->name('penutup-lha-rekomendasi.tindak-lanjut.form');
        Route::post('penutup-lha-rekomendasi/{rekomendasi}/tindak-lanjut', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'storeTindakLanjut'])->name('penutup-lha-rekomendasi.tindak-lanjut.store');
    });

    // Monitoring & Progress Tindak Lanjut (AUDITEE + VIEW BOD bisa akses)
    Route::middleware(['auth', 'role:KSPI,ASMAN SPI,AUDITOR,AUDITEE,SUPER ADMIN,VIEW BOD'])->group(function () {
        Route::get('monitoring-tindak-lanjut', [\App\Http\Controllers\Audit\MonitoringTindakLanjutController::class, 'index'])->name('monitoring-tindak-lanjut.index');
        Route::get('progress-tindak-lanjut', [\App\Http\Controllers\Audit\ProgressTindakLanjutController::class, 'index'])->name('progress-tindak-lanjut.index');
    });

    // =========================================================
    // HALAMAN SPI TEAM + VIEW BOD (CRUD untuk SPI, view untuk BOD)
    // =========================================================
    Route::middleware(['auth', 'role:KSPI,ASMAN SPI,AUDITOR,SUPER ADMIN,VIEW BOD'])->group(function () {

        // Perencanaan Audit
        Route::get('perencanaan/get-nomor-surat-tugas', [PerencanaanAuditController::class, 'getNomorSuratTugas'])->name('perencanaan.get-nomor-surat-tugas');
        Route::resource('perencanaan', PerencanaanAuditController::class);

        // Jadwal PKPT (hapus approval)
        Route::resource('pkpt', \App\Http\Controllers\Http\Controllers\Audit\JadwalPkptAuditController::class);
        // NOTE: Route pkpt.approval DIHAPUS sesuai rancangan RBAC

        // Program Kerja Audit (PKA)
        Route::get('pka/hierarki-flat/{perencanaanId}', [\App\Http\Controllers\Http\Controllers\Audit\ProgramKerjaAuditController::class, 'getHierarkiFlat'])->name('pka.hierarki-flat');
        Route::resource('pka', \App\Http\Controllers\Http\Controllers\Audit\ProgramKerjaAuditController::class);
        Route::get('pka/{pka}/download', [\App\Http\Controllers\Http\Controllers\Audit\ProgramKerjaAuditController::class, 'download'])->name('pka.download');
        Route::get('pka/{pka}/check-relations', [\App\Http\Controllers\Http\Controllers\Audit\ProgramKerjaAuditController::class, 'checkRelations'])->name('pka.check-relations');
        Route::post('pka/{pka}/dokumen/{dok}/approval', [\App\Http\Controllers\Http\Controllers\Audit\ProgramKerjaAuditController::class, 'approval'])->name('pka.approval');
        Route::post('pka/{id}/approval-main', [\App\Http\Controllers\Http\Controllers\Audit\ProgramKerjaAuditController::class, 'approvalMain'])->name('pka.approval-main');

        // Walkthrough
        Route::resource('walkthrough', \App\Http\Controllers\Audit\WalkthroughAuditController::class);
        Route::post('walkthrough/{walkthrough}/approval', [\App\Http\Controllers\Audit\WalkthroughAuditController::class, 'approval'])->name('walkthrough.approval');

        // TOD BPM
        Route::resource('tod-bpm', \App\Http\Controllers\Audit\TodBpmAuditController::class);
        Route::post('tod-bpm/{tod_bpm}/approval', [\App\Http\Controllers\Audit\TodBpmAuditController::class, 'approval'])->name('tod-bpm.approval');
        Route::resource('tod-bpm-evaluasi', \App\Http\Controllers\Audit\TodBpmEvaluasiController::class);
        Route::get('tod-bpm-evaluasi-modal/{bpmId}', [\App\Http\Controllers\Audit\TodBpmEvaluasiController::class, 'modal'])->name('tod-bpm-evaluasi.modal');

        // TOE
        Route::resource('toe', ToeAuditController::class);
        Route::post('toe/{id}/approval', [ToeAuditController::class, 'approval'])->name('toe.approval');
        Route::get('toe-evaluasi', [ToeEvaluasiController::class, 'index'])->name('toe-evaluasi.index');
        Route::post('toe-evaluasi', [ToeEvaluasiController::class, 'store'])->name('toe-evaluasi.store');
        Route::put('toe-evaluasi/{id}', [ToeEvaluasiController::class, 'update'])->name('toe-evaluasi.update');
        Route::delete('toe-evaluasi/{id}', [ToeEvaluasiController::class, 'destroy'])->name('toe-evaluasi.destroy');
        Route::get('toe-evaluasi-modal/{toeId}', [ToeEvaluasiController::class, 'modal'])->name('toe-evaluasi.modal');

        // Entry Meeting
        Route::resource('entry-meeting', EntryMeetingController::class);
        Route::post('entry-meeting/{id}/approval', [EntryMeetingController::class, 'approval'])->name('entry-meeting.approval');

        // Exit Meeting (bukan upload dokumen — ini adalah entry meeting audit)
        Route::resource('exit-meeting', \App\Http\Controllers\Audit\ExitMeetingController::class)->except(['show']);
        Route::post('exit-meeting/{id}/approval', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'approval'])->name('exit-meeting.approval');

        // Pelaporan Hasil Audit — CREATE/EDIT/DELETE hanya SPI (non-AUDITEE)
        Route::post('pelaporan-hasil-audit', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'store'])->name('pelaporan-hasil-audit.store');
        Route::get('pelaporan-hasil-audit/create', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'create'])->name('pelaporan-hasil-audit.create');
        Route::get('pelaporan-hasil-audit/{id}/edit', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'edit'])->name('pelaporan-hasil-audit.edit');
        Route::put('pelaporan-hasil-audit/{id}', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'update'])->name('pelaporan-hasil-audit.update');
        Route::delete('pelaporan-hasil-audit/{id}', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'destroy'])->name('pelaporan-hasil-audit.destroy');
        Route::post('pelaporan-hasil-audit/{id}/approval', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'approval'])->name('pelaporan-hasil-audit.approval');
        Route::post('pelaporan-hasil-audit/generate-nomor-lhk', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'generateNomorLhk'])->name('pelaporan-hasil-audit.generate-nomor-lhk');
        Route::post('pelaporan-hasil-audit/generate-nomor-iss', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'generateNomorIss'])->name('pelaporan-hasil-audit.generate-nomor-iss');
        Route::get('pelaporan-hasil-audit/{id}/temuan', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'getTemuanData'])->name('pelaporan-hasil-audit.get-temuan');
        Route::get('pelaporan-hasil-audit/temuan/{id}', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'getTemuanById'])->name('pelaporan-hasil-audit.get-temuan-by-id');
        Route::put('pelaporan-hasil-audit/temuan/{id}', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'updateTemuan'])->name('pelaporan-hasil-audit.update-temuan');
        Route::get('pelaporan-hasil-audit/temuan-for-penutup', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'getAllTemuanForPenutup'])->name('pelaporan-hasil-audit.temuan-for-penutup');
        Route::get('test-temuan/{id}', [\App\Http\Controllers\Audit\PelaporanHasilAuditController::class, 'getTemuanById'])->name('test.temuan');

        // Penutup LHA Rekomendasi
        Route::get('penutup-lha-rekomendasi/select-nomor-surat-tugas', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'selectNomorSuratTugas'])->name('penutup-lha-rekomendasi.select-nomor-surat-tugas');
        Route::resource('penutup-lha-rekomendasi', \App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class);
        Route::post('penutup-lha-rekomendasi/{id}/approval', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'approval'])->name('penutup-lha-rekomendasi.approval');
        Route::get('penutup-lha-rekomendasi/get-iss-data', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'getIssData'])->name('penutup-lha-rekomendasi.get-iss-data');
        Route::get('penutup-lha-tindak-lanjut/{id}/edit', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'editTindakLanjut'])->name('penutup-lha-tindak-lanjut.edit');
        Route::put('penutup-lha-tindak-lanjut/{id}', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'updateTindakLanjut'])->name('penutup-lha-tindak-lanjut.update');
        Route::delete('penutup-lha-tindak-lanjut/{id}', [\App\Http\Controllers\Audit\PenutupLhaRekomendasiController::class, 'destroyTindakLanjut'])->name('penutup-lha-tindak-lanjut.destroy');

        // Pemantauan — aksi yang butuh SPI (edit, update, kirim reminder)
        Route::get('pemantauan/{id}/edit', [\App\Http\Controllers\Audit\PemantauanAuditController::class, 'edit'])->name('pemantauan.edit');
        Route::put('pemantauan/{id}', [\App\Http\Controllers\Audit\PemantauanAuditController::class, 'update'])->name('pemantauan.update');
        Route::post('pemantauan/{id}/update-status', [\App\Http\Controllers\Audit\PemantauanAuditController::class, 'updateStatus'])->name('pemantauan.update-status');
        Route::post('pemantauan/{id}/kirim-reminder', [\App\Http\Controllers\Audit\PemantauanAuditController::class, 'sendReminder'])->name('pemantauan.send-reminder');

        // Dashboard
        Route::get('dashboard-pkpt', [\App\Http\Controllers\Audit\DashboardPkptController::class, 'index'])->name('dashboard-pkpt.index');
        Route::get('dashboard-rencana-pkpt', [\App\Http\Controllers\Audit\DashboardRencanaPkptController::class, 'index'])->name('dashboard-rencana-pkpt.index');
        Route::get('rekapitulasi-aktivitas', [\App\Http\Controllers\Audit\RekapitulasiAktivitasAuditController::class, 'index'])->name('rekapitulasi-aktivitas.index');

        // Exit Meeting Chart (dashboard)
        Route::get('exit-meeting/pie', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'pie'])->name('exit-meeting.pie');
        Route::get('exit-meeting/chart', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'chart'])->name('exit-meeting.chart');
    });
});

// Simple routes for exit meeting management
Route::prefix('exit-meeting')->name('exit-meeting.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'chart'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Audit\ExitMeetingController::class, 'destroy'])->name('destroy');
});
