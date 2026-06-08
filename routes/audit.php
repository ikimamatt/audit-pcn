<?php

use Illuminate\Support\Facades\Route;

// === Perencanaan Audit ===
use App\Http\Controllers\Audit\PerencanaanAudit\PerencanaanAuditController;
use App\Http\Controllers\Audit\PerencanaanAudit\JadwalPkptAuditController;
use App\Http\Controllers\Audit\PerencanaanAudit\ProgramKerjaAuditController;

// === Pelaksanaan Audit ===
use App\Http\Controllers\Audit\PelaksanaanAudit\EntryMeetingController;
use App\Http\Controllers\Audit\PelaksanaanAudit\ExitMeetingController;
use App\Http\Controllers\Audit\PelaksanaanAudit\WalkthroughAuditController;
use App\Http\Controllers\Audit\PelaksanaanAudit\TodBpmAuditController;
use App\Http\Controllers\Audit\PelaksanaanAudit\TodBpmEvaluasiController;
use App\Http\Controllers\Audit\PelaksanaanAudit\ToeAuditController;
use App\Http\Controllers\Audit\PelaksanaanAudit\ToeEvaluasiController;

// === Pelaporan Audit ===
use App\Http\Controllers\Audit\PelaporanAudit\PelaporanHasilAuditController;
use App\Http\Controllers\Audit\PelaporanAudit\PelaporanTemuanController;
use App\Http\Controllers\Audit\PelaporanAudit\PenutupLhaRekomendasiController;
use App\Http\Controllers\Audit\PelaporanAudit\PersetujuanController;

// === Tindak Lanjut ===
use App\Http\Controllers\Audit\TindakLanjut\PemantauanAuditController;
use App\Http\Controllers\Audit\TindakLanjut\MonitoringTindakLanjutController;
use App\Http\Controllers\Audit\TindakLanjut\ProgressTindakLanjutController;

// === Dashboard ===
use App\Http\Controllers\Audit\Dashboard\DashboardAnalitikController;
use App\Http\Controllers\Audit\Dashboard\DashboardPkptController;
use App\Http\Controllers\Audit\Dashboard\DashboardRencanaPkptController;
use App\Http\Controllers\Audit\Dashboard\RekapitulasiAktivitasAuditController;

// ============================================================
// AUDIT ROUTES
// ============================================================
Route::pattern('pkpt', '[0-9]+');
Route::pattern('pka', '[0-9]+');
Route::pattern('tod_bpm', '[0-9]+');
Route::pattern('toe', '[0-9]+');

Route::prefix('audit')->name('audit.')->group(function () {

    // ----------------------------------------------------------
    // HALAMAN AUDITEE-ACCESSIBLE (view only)
    // Role: KSPI, ASMAN SPI, AUDITOR, AUDITEE, SUPER ADMIN, VIEW BOD
    // ----------------------------------------------------------
    // ----------------------------------------------------------
    // HALAMAN AUDITEE-ACCESSIBLE (view only)
    // Role: KSPI, ASMAN SPI, AUDITOR, AUDITEE, SUPER ADMIN, VIEW BOD
    // ----------------------------------------------------------
    Route::middleware(['auth', 'role:KSPI,ASMAN SPI,AUDITOR,AUDITEE,SUPER ADMIN,VIEW BOD'])->group(function () {

        // Pelaporan Hasil Audit
        Route::get('pelaporan-hasil-audit', [PelaporanHasilAuditController::class, 'index'])->name('pelaporan-hasil-audit.index');
        Route::get('pelaporan-hasil-audit/{id}', [PelaporanHasilAuditController::class, 'show'])->name('pelaporan-hasil-audit.show')->where('id', '[0-9]+');
        Route::get('pelaporan-hasil-audit/{id}/temuan', [PelaporanHasilAuditController::class, 'getTemuanData'])->name('pelaporan-hasil-audit.get-temuan');
        Route::get('pelaporan-hasil-audit/temuan/{id}', [PelaporanHasilAuditController::class, 'getTemuanById'])->name('pelaporan-hasil-audit.get-temuan-by-id');

        // Pemantauan Hasil Audit
        Route::get('pemantauan/select-nomor-surat-tugas', [PemantauanAuditController::class, 'selectNomorSuratTugas'])->name('pemantauan.select-nomor-surat-tugas');
        Route::get('pemantauan', [PemantauanAuditController::class, 'index'])->name('pemantauan.index');
        Route::get('pemantauan/{id}/tindak-lanjut', [PemantauanAuditController::class, 'tindakLanjutIndex'])->name('pemantauan.tindak-lanjut.index');
        Route::post('pemantauan/{id}/update-status', [PemantauanAuditController::class, 'updateStatus'])->name('pemantauan.update-status');

        // Penutup LHA Rekomendasi (view only)
        Route::get('penutup-lha-rekomendasi/select-nomor-surat-tugas', [PenutupLhaRekomendasiController::class, 'selectNomorSuratTugas'])->name('penutup-lha-rekomendasi.select-nomor-surat-tugas');
        Route::get('penutup-lha-rekomendasi/get-iss-data', [PenutupLhaRekomendasiController::class, 'getIssData'])->name('penutup-lha-rekomendasi.get-iss-data');
        Route::get('penutup-lha-rekomendasi', [PenutupLhaRekomendasiController::class, 'index'])->name('penutup-lha-rekomendasi.index');
        Route::get('penutup-lha-rekomendasi/{penutup_lha_rekomendasi}', [PenutupLhaRekomendasiController::class, 'show'])->name('penutup-lha-rekomendasi.show')->where('penutup_lha_rekomendasi', '[0-9]+');
        Route::post('penutup-lha-rekomendasi/{id}/approval', [PenutupLhaRekomendasiController::class, 'approval'])->name('penutup-lha-rekomendasi.approval');

        // Tindak Lanjut Form untuk PIC Business Contact (AUDITEE)
        Route::get('penutup-lha-rekomendasi/{rekomendasi}/tindak-lanjut', [PenutupLhaRekomendasiController::class, 'tindakLanjutForm'])->name('penutup-lha-rekomendasi.tindak-lanjut.form');
        Route::post('penutup-lha-rekomendasi/{rekomendasi}/tindak-lanjut', [PenutupLhaRekomendasiController::class, 'storeTindakLanjut'])->name('penutup-lha-rekomendasi.tindak-lanjut.store');

        // Monitoring & Progress Tindak Lanjut
        Route::get('monitoring-tindak-lanjut', [MonitoringTindakLanjutController::class, 'index'])->name('monitoring-tindak-lanjut.index');
        Route::get('progress-tindak-lanjut', [ProgressTindakLanjutController::class, 'index'])->name('progress-tindak-lanjut.index');

        // Persetujuan Dokumen
        Route::get('persetujuan', [PersetujuanController::class, 'index'])->name('persetujuan.index');
        Route::post('persetujuan/proses', [PersetujuanController::class, 'proses'])->name('persetujuan.proses');
    });

    // ----------------------------------------------------------
    // HALAMAN SPI TEAM + VIEW BOD (View Only untuk BOD & SPI)
    // Role: KSPI, ASMAN SPI, AUDITOR, SUPER ADMIN, VIEW BOD
    // ----------------------------------------------------------
    Route::middleware(['auth', 'role:KSPI,ASMAN SPI,AUDITOR,SUPER ADMIN,VIEW BOD'])->group(function () {

        // --- Perencanaan Audit ---
        Route::get('perencanaan/get-nomor-surat-tugas', [PerencanaanAuditController::class, 'getNomorSuratTugas'])->name('perencanaan.get-nomor-surat-tugas');
        Route::resource('perencanaan', PerencanaanAuditController::class)->only(['index']);

        // --- Jadwal PKPT ---
        Route::resource('pkpt', JadwalPkptAuditController::class)->only(['index', 'show']);

        // --- Program Kerja Audit (PKA) ---
        Route::get('pka/hierarki-flat/{perencanaanId}', [ProgramKerjaAuditController::class, 'getHierarkiFlat'])->name('pka.hierarki-flat');
        Route::resource('pka', ProgramKerjaAuditController::class)->only(['index', 'show']);
        Route::get('pka/{pka}/download', [ProgramKerjaAuditController::class, 'download'])->name('pka.download');

        // --- Walkthrough ---
        Route::resource('walkthrough', WalkthroughAuditController::class)->only(['index']);

        // --- TOD BPM ---
        Route::resource('tod-bpm', TodBpmAuditController::class)->only(['index', 'show']);
        Route::resource('tod-bpm-evaluasi', TodBpmEvaluasiController::class)->only(['index']);
        Route::get('tod-bpm-evaluasi-modal/{bpmId}', [TodBpmEvaluasiController::class, 'modal'])->name('tod-bpm-evaluasi.modal');

        // --- TOE ---
        Route::resource('toe', ToeAuditController::class)->only(['index', 'show']);
        Route::get('toe-evaluasi', [ToeEvaluasiController::class, 'index'])->name('toe-evaluasi.index');
        Route::get('toe-evaluasi-modal/{toeId}', [ToeEvaluasiController::class, 'modal'])->name('toe-evaluasi.modal');

        // --- Entry Meeting ---
        Route::resource('entry-meeting', EntryMeetingController::class)->only(['index']);

        // --- Exit Meeting ---
        Route::resource('exit-meeting', ExitMeetingController::class)->only(['index']);
        Route::get('exit-meeting/pie', [ExitMeetingController::class, 'pie'])->name('exit-meeting.pie');
        Route::get('exit-meeting/chart', [ExitMeetingController::class, 'chart'])->name('exit-meeting.chart');

        // --- Pelaporan Hasil Audit ---
        Route::get('pelaporan-hasil-audit/temuan-for-penutup', [PelaporanHasilAuditController::class, 'getAllTemuanForPenutup'])->name('pelaporan-hasil-audit.temuan-for-penutup');
        Route::get('test-temuan/{id}', [PelaporanHasilAuditController::class, 'getTemuanById'])->name('test.temuan');

        // --- Dashboard ---
        Route::get('dashboard-pkpt', [DashboardPkptController::class, 'index'])->name('dashboard-pkpt.index');
        Route::get('dashboard-rencana-pkpt', [DashboardRencanaPkptController::class, 'index'])->name('dashboard-rencana-pkpt.index');
        Route::get('rekapitulasi-aktivitas', [RekapitulasiAktivitasAuditController::class, 'index'])->name('rekapitulasi-aktivitas.index');
        Route::get('dashboard', [DashboardAnalitikController::class, 'index'])->name('dashboard');
        Route::get('dashboard/aging-detail', [DashboardAnalitikController::class, 'agingDetail'])->name('dashboard.aging-detail');
    });

    // ----------------------------------------------------------
    // AKSI MODIFIKASI SPI TEAM (CRUD, Approval, Reminder)
    // Role: KSPI, ASMAN SPI, AUDITOR, SUPER ADMIN (VIEW BOD & AUDITEE blocked)
    // ----------------------------------------------------------
    Route::middleware(['auth', 'role:KSPI,ASMAN SPI,AUDITOR,SUPER ADMIN', 'can-modify'])->group(function () {

        // --- Perencanaan Audit ---
        Route::resource('perencanaan', PerencanaanAuditController::class)->except(['index', 'show']);

        // --- Jadwal PKPT ---
        Route::resource('pkpt', JadwalPkptAuditController::class)->except(['index', 'show']);

        // --- Program Kerja Audit (PKA) ---
        Route::resource('pka', ProgramKerjaAuditController::class)->except(['index', 'show']);
        Route::get('pka/{pka}/check-relations', [ProgramKerjaAuditController::class, 'checkRelations'])->name('pka.check-relations');
        Route::post('pka/{pka}/dokumen/{dok}/approval', [ProgramKerjaAuditController::class, 'approval'])->name('pka.approval');
        Route::post('pka/{id}/approval-main', [ProgramKerjaAuditController::class, 'approvalMain'])->name('pka.approval-main');

        // --- Walkthrough ---
        Route::resource('walkthrough', WalkthroughAuditController::class)->except(['index', 'show']);
        Route::post('walkthrough/{walkthrough}/approval', [WalkthroughAuditController::class, 'approval'])->name('walkthrough.approval');

        // --- TOD BPM ---
        Route::resource('tod-bpm', TodBpmAuditController::class)->except(['index', 'show']);
        Route::post('tod-bpm/{tod_bpm}/approval', [TodBpmAuditController::class, 'approval'])->name('tod-bpm.approval');
        Route::resource('tod-bpm-evaluasi', TodBpmEvaluasiController::class)->except(['index', 'show']);

        // --- TOE ---
        Route::resource('toe', ToeAuditController::class)->except(['index', 'show']);
        Route::post('toe/{id}/approval', [ToeAuditController::class, 'approval'])->name('toe.approval');
        Route::post('toe-evaluasi', [ToeEvaluasiController::class, 'store'])->name('toe-evaluasi.store');
        Route::put('toe-evaluasi/{id}', [ToeEvaluasiController::class, 'update'])->name('toe-evaluasi.update');
        Route::delete('toe-evaluasi/{id}', [ToeEvaluasiController::class, 'destroy'])->name('toe-evaluasi.destroy');

        // --- Entry Meeting ---
        Route::resource('entry-meeting', EntryMeetingController::class)->except(['index', 'show']);
        Route::post('entry-meeting/{id}/approval', [EntryMeetingController::class, 'approval'])->name('entry-meeting.approval');

        // --- Exit Meeting ---
        Route::resource('exit-meeting', ExitMeetingController::class)->except(['index', 'show']);
        Route::post('exit-meeting/{id}/approval', [ExitMeetingController::class, 'approval'])->name('exit-meeting.approval');

        // --- Pelaporan Hasil Audit ---
        Route::post('pelaporan-hasil-audit', [PelaporanHasilAuditController::class, 'store'])->name('pelaporan-hasil-audit.store');
        Route::get('pelaporan-hasil-audit/create', [PelaporanHasilAuditController::class, 'create'])->name('pelaporan-hasil-audit.create');
        Route::get('pelaporan-hasil-audit/{id}/edit', [PelaporanHasilAuditController::class, 'edit'])->name('pelaporan-hasil-audit.edit');
        Route::put('pelaporan-hasil-audit/{id}', [PelaporanHasilAuditController::class, 'update'])->name('pelaporan-hasil-audit.update');
        Route::delete('pelaporan-hasil-audit/{id}', [PelaporanHasilAuditController::class, 'destroy'])->name('pelaporan-hasil-audit.destroy');
        Route::post('pelaporan-hasil-audit/{id}/approval', [PelaporanHasilAuditController::class, 'approval'])->name('pelaporan-hasil-audit.approval');
        Route::post('pelaporan-hasil-audit/generate-nomor-lhk', [PelaporanHasilAuditController::class, 'generateNomorLhk'])->name('pelaporan-hasil-audit.generate-nomor-lhk');
        Route::post('pelaporan-hasil-audit/generate-nomor-iss', [PelaporanHasilAuditController::class, 'generateNomorIss'])->name('pelaporan-hasil-audit.generate-nomor-iss');
        Route::put('pelaporan-hasil-audit/temuan/{id}', [PelaporanHasilAuditController::class, 'updateTemuan'])->name('pelaporan-hasil-audit.update-temuan');

        // --- Penutup LHA Rekomendasi ---
        Route::resource('penutup-lha-rekomendasi', PenutupLhaRekomendasiController::class)->except(['index', 'show']);
        Route::get('penutup-lha-tindak-lanjut/{id}/edit', [PenutupLhaRekomendasiController::class, 'editTindakLanjut'])->name('penutup-lha-tindak-lanjut.edit');
        Route::put('penutup-lha-tindak-lanjut/{id}', [PenutupLhaRekomendasiController::class, 'updateTindakLanjut'])->name('penutup-lha-tindak-lanjut.update');
        Route::delete('penutup-lha-tindak-lanjut/{id}', [PenutupLhaRekomendasiController::class, 'destroyTindakLanjut'])->name('penutup-lha-tindak-lanjut.destroy');

        // --- Pemantauan ---
        Route::get('pemantauan/{id}/edit', [PemantauanAuditController::class, 'edit'])->name('pemantauan.edit');
        Route::put('pemantauan/{id}', [PemantauanAuditController::class, 'update'])->name('pemantauan.update');
        Route::post('pemantauan/{id}/kirim-reminder', [PemantauanAuditController::class, 'sendReminder'])->name('pemantauan.send-reminder');
    });
});
