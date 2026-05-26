<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\MasterKodeAoiController;
use App\Http\Controllers\MasterData\MasterKodeRiskController;
use App\Http\Controllers\MasterData\MasterAuditeeController;
use App\Http\Controllers\MasterData\MasterUserController;
use App\Http\Controllers\MasterData\MasterAksesUserController;
use App\Http\Controllers\MasterData\MasterJenisAuditController;
use App\Http\Controllers\MasterData\MasterAreaController;
use App\Http\Controllers\MasterData\MasterSubBidangController;

// Master Data Routes — hanya KSPI, ASMAN SPI, dan SUPER ADMIN
Route::middleware(['auth', 'role:KSPI,ASMAN SPI,SUPER ADMIN'])
    ->prefix('master')
    ->name('master.')
    ->group(function () {
        // Kode AOI
        Route::resource('kode-aoi', MasterKodeAoiController::class)
            ->names('kode-aoi')
            ->parameters(['kode-aoi' => 'masterKodeAoi']);

        // Kode Risk
        Route::resource('kode-risk', MasterKodeRiskController::class)
            ->names('kode-risk')
            ->parameters(['kode-risk' => 'masterKodeRisk']);

        // Bidang (formerly Auditee)
        Route::resource('auditee', MasterAuditeeController::class)
            ->names('auditee')
            ->parameters(['auditee' => 'masterAuditee']);

        // Endpoint AJAX: ambil sub bidang untuk bidang tertentu
        Route::get('auditee/{masterAuditee}/sub-bidang', [MasterAuditeeController::class, 'getSubBidang'])
            ->name('auditee.sub-bidang');

        // Sub Bidang (AJAX — digunakan dari Offcanvas di halaman Master Bidang)
        Route::post('sub-bidang', [MasterSubBidangController::class, 'store'])
            ->name('sub-bidang.store');
        Route::put('sub-bidang/{masterSubBidang}', [MasterSubBidangController::class, 'update'])
            ->name('sub-bidang.update');
        Route::delete('sub-bidang/{masterSubBidang}', [MasterSubBidangController::class, 'destroy'])
            ->name('sub-bidang.destroy');

        // User
        Route::resource('user', MasterUserController::class)
            ->names('user')
            ->parameters(['user' => 'masterUser']);
        Route::post('user/{masterUser}/reset-password', [MasterUserController::class, 'resetPassword'])
            ->name('user.reset-password');

        // Akses User
        Route::get('akses-user', [MasterAksesUserController::class, 'index'])->name('akses-user.index');

        // Jenis Audit
        Route::resource('jenis-audit', MasterJenisAuditController::class)
            ->names('jenis-audit')
            ->parameters(['jenis-audit' => 'masterJenisAudit']);

        // Area
        Route::resource('area', MasterAreaController::class)
            ->names('area')
            ->parameters(['area' => 'masterArea']);
    });