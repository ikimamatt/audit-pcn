<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\MasterKodeAoiController;
use App\Http\Controllers\MasterData\MasterKodeRiskController;
use App\Http\Controllers\MasterData\MasterAuditeeController;
use App\Http\Controllers\MasterData\MasterUserController;
use App\Http\Controllers\MasterData\MasterAksesUserController;
use App\Http\Controllers\MasterData\MasterJenisAuditController;
use App\Http\Controllers\MasterData\MasterUnitController;

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

        // Auditee
        Route::resource('auditee', MasterAuditeeController::class)
            ->names('auditee')
            ->parameters(['auditee' => 'masterAuditee']);

        // User
        Route::resource('user', MasterUserController::class)
            ->names('user')
            ->parameters(['user' => 'masterUser']);

        // Akses User
        Route::get('akses-user', [MasterAksesUserController::class, 'index'])->name('akses-user.index');

        // Jenis Audit
        Route::resource('jenis-audit', MasterJenisAuditController::class)
            ->names('jenis-audit')
            ->parameters(['jenis-audit' => 'masterJenisAudit']);

        // Unit
        Route::resource('unit', MasterUnitController::class)
            ->names('unit')
            ->parameters(['unit' => 'masterUnit']);
    });